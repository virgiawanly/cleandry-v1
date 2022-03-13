const outletId = $("meta[name='outlet-id']").attr("content");
let itemsTable;
let membersTable;
let servicesTable;
let totalPrice;
let totalPayment;

const updateSubTotal = function () {
    setTimeout(() => {
        let price = parseInt(
            $(this).closest("tr").find(".int-price").text() || 0
        );
        let qty = parseInt(
            $(this).closest("tr").find('input[name="qty[]"]').val() || 0
        );
        let subtotal = qty * price;
        $(this).closest("tr").find(".int-subtotal").text(subtotal);
        $(this)
            .closest("tr")
            .find(".o-subtotal")
            .text(formatter.format(subtotal));
        updateTotalPrice();
    });
};

const updateRowNumber = function () {
    setTimeout(() => {
        let tr = $("#transaction-items-table").find("tbody tr");
        tr.each((i, row) => {
            $(row)
                .find("td:eq(0)")
                .text(++i);
        });
    });
};

const updateTotalPrice = function () {
    setTimeout(() => {
        let prices = $(".int-subtotal")
            .map((i, el) => {
                return parseInt($(el).text() || 0);
            })
            .get();
        totalPrice = prices.reduce((acc, curr) => {
            return acc + curr;
        }, 0);
        $(".o-total-price").val(formatter.format(totalPrice));
        updateTotalPayment();
    });
};

const updateTotalPayment = function () {
    setTimeout(() => {
        let discount = parseFloat($('[name="discount"]').val() || 0);
        let discountType = $('[name="discount_type"]').val();
        let tax = parseInt($('[name="tax"]').val() || 0);
        let additionalCost = parseInt($('[name="additional_cost"]').val() || 0);

        let totalDiscount =
            discountType == "percent"
                ? (discount / 100) * totalPrice
                : discount;
        let totalTax = (tax / 100) * totalPrice;
        let totalPayment =
            totalPrice - totalDiscount + totalTax + additionalCost;

        $(".o-total-payment").val(formatter.format(totalPayment));
    });
};

const submitHandler = async () => {
    event.preventDefault();
    let url = $("#transaction-form").attr("action");
    let formData = $("#transaction-form").serialize();
    $(".submit-transaction-button").attr("disabled", true);
    try {
        let res = await $.post(url, formData);
        console.log(res);
        let next = await Swal.fire({
            title: "Transaksi Berhasil",
            icon: "success",
            showCancelButton: true,
            confirmButtonColor: "#28A745",
            cancelButtonColor: "#037AFC",
            confirmButtonText:
                '<i class="fas fa-print mr-1"></i><span>Cetak Faktur</span>',
            cancelButtonText:
                '<i class="fas fa-sync mr-1"></i><span>Transaksi Lagi</span>',
        });
        if (next.isConfirmed) {
            document.location.href = `/o/${outletId}/transactions/${res.transaction.id}/invoice`;
        } else {
            document.location.reload();
        }
    } catch (err) {
        if (err.status === 422) validationErrorHandler(err.responseJSON.errors);
        toast("Gagal", "error");
    }
    $(".submit-transaction-button").attr("disabled", false);
};

$(function () {
    totalPrice = 0;
    totalPayment = 0;

    itemsTable = $("#transaction-items-table").DataTable({
        language: {
            zeroRecords: " ",
        },
        sort: false,
        paging: false,
        info: false,
        searching: false,
        lengthChange: false,
    });

    membersTable = $("#members-table").DataTable({
        ...DATATABLE_OPTIONS,
        ajax: `/o/${outletId}/transactions/new-transaction/get-members`,
        columns: [
            {
                data: "DT_RowIndex",
            },
            {
                data: "name",
            },
            {
                data: "phone",
            },
            {
                data: "address",
            },
            {
                data: "id",
                render: function (id) {
                    return `<button class="btn btn-success select-member-button" data-id="${id}"><i class="fas fa-check-circle mr-1"></i><span>Pilih</span></button>`;
                },
            },
        ],
    });

    servicesTable = $("#services-table").DataTable({
        ajax: `/o/${outletId}/transactions/new-transaction/get-services`,
        columns: [
            {
                data: "DT_RowIndex",
            },
            {
                data: "name",
            },
            {
                data: "type",
                render: (type) => type.name,
            },
            {
                data: "unit",
            },
            {
                data: "price",
            },
            {
                data: "id",
                render: (id) =>
                    `<button class="btn btn-success select-item-button" data-service-id="${id}"><i class="fas fa-check-circle mr-1"></i><span>Pilih</span></button>`,
            },
        ],
    });

    $("#transaction-form").on("submit", submitHandler);

    $("#open-member-modal-button").on("click", function () {
        $("#select-member-modal").modal("show");
    });

    $('[name="payment_status"]').on("change", function () {
        if ($(this).val() == "paid") {
            $("#pay-now").removeClass("d-none");
            $("#pay-now").addClass("d-block");
        } else {
            $("#pay-now").removeClass("d-block");
            $("#pay-now").addClass("d-none");
        }
    });

    $('[name="discount"]').on("keyup change", updateTotalPayment);
    $('[name="discount_type"]').on("keyup change", updateTotalPayment);
    $('[name="tax"]').on("keyup change", updateTotalPayment);
    $('[name="additional_cost"]').on("keyup change", updateTotalPayment);

    $("#members-table").on("click", ".select-member-button", function () {
        let tr = $(this).closest("tr");
        let memberId = $(this).data("id");
        let memberName = tr.find("td:eq(1)").text();
        let memberPhone = tr.find("td:eq(2)").text();
        $("input.o-member-info").val(`${memberName} (${memberPhone})`);
        $('input[name="member_id"]').val(memberId);
        $("#select-member-modal").modal("hide");
        $("#open-member-info-modal-button").attr("disabled", false);
    });

    $("#open-member-info-modal-button").on("click", async function () {
        let modal = $("#member-info-modal");
        let memberId = $("[name=member_id]").val();
        let url = `/o/${outletId}/members/${memberId}`;
        modal.modal("show");
        modal.find(".o-member-name").text("");
        modal.find(".o-member-phone").text("");
        modal.find(".o-member-email").text("");
        modal.find(".o-member-gender").text("");
        modal.find(".o-member-address").text("");
        try {
            let res = await fetchData(url);
            modal.find(".o-member-name").text(res.member.name);
            modal.find(".o-member-phone").text(res.member.phone);
            modal.find(".o-member-email").text(res.member.email);
            modal
                .find(".o-member-gender")
                .text(res.member.gender == "M" ? "Laki-laki" : "Perempuan");
            modal.find(".o-member-address").text(res.member.address);
        } catch (err) {
            toast("Tidak dapat mengambil data", "error");
        }
    });

    $("#open-add-item-modal-button").on("click", function () {
        $("#add-item-modal").modal("show");
    });

    $("#search-item-input").on("keydown", function () {
        event.preventDefault();
        $("#add-item-modal").modal("show");
    });

    $("#services-table").on("click", ".select-item-button", function () {
        let table = $("#transaction-items-table");
        let serviceId = $(this).data("service-id");
        let arrItemsId = table
            .find("tbody tr")
            .map(function (i, row) {
                let id = $(row).find('input[name="service_id[]"]').eq(0).val();
                return parseInt(id || null);
            })
            .get();

        if (arrItemsId.some((id) => serviceId == id)) {
            let tr = $(
                `input[name="service_id[]"][value="${serviceId}"]`
            ).closest("tr");
            let inputQty = tr.find('input[name="qty[]"]');
            inputQty.val(function () {
                return parseInt($(this).val() || 0) + 1;
            });
            inputQty.trigger("change");
        } else {
            let row = $(this).closest("tr");
            let rowNumber = arrItemsId.length + 1;
            let serviceName = `<span>${row.find("td").eq(1).text()}</span>
        <input type="hidden" name="service_id[]" value="${serviceId}">`;
            let serviceType = `<span>${row.find("td").eq(2).text()}</span>`;
            let serviceUnit = `<span>${row.find("td").eq(3).text()}</span>`;
            let servicePrice = `<span>${formatter.format(
                row.find("td").eq(4).text()
            )}</span><span class="int-price d-none">${row
                .find("td")
                .eq(4)
                .text()}</span>`;
            let inputQty = `<input type="number" class="form-control" name="qty[]" value="1" min="1">`;
            let subtotal = `<span class="o-subtotal">${formatter.format(
                row.find("td").eq(4).text()
            )}</span><span class="int-subtotal d-none">${row
                .find("td")
                .eq(4)
                .text()}</span>`;
            let actions = `<button type="button" class="button-hapus-barang btn btn-sm btn-danger remove-item-button" title="Hapus Barang"><i class="fas fa-trash"></i></button>`;
            itemsTable.row
                .add([
                    rowNumber,
                    serviceName,
                    serviceType,
                    serviceUnit,
                    servicePrice,
                    inputQty,
                    subtotal,
                    actions,
                ])
                .draw();
        }
        $("#add-item-modal").modal("hide");
        updateRowNumber();
        updateTotalPrice();
    });

    $("#transaction-items-table").on(
        "click",
        ".remove-item-button",
        function () {
            itemsTable.row($(this).parents("tr")).remove().draw();
            updateRowNumber();
            updateTotalPrice();
        }
    );

    $("#transaction-items-table").on(
        "keydown change",
        'input[name="qty[]"]',
        updateSubTotal
    );
});
