let table;
const outletId = $("meta[name='outlet-id']").attr("content");
let datatableUrl = `/o/${outletId}/transactions/datatable`;
let lastPaymentDetail;

$(function () {
    table = $("#transactions-table").DataTable({
        ...DATATABLE_OPTIONS,
        ajax: {
            url: `${datatableUrl}?status=new`,
        },
        columns: [
            {
                data: "DT_RowIndex",
            },
            {
                data: "invoice",
            },
            {
                data: "member",
                render: (member) => member.name,
            },
            {
                data: "total_item",
            },
            {
                data: "date",
            },
            {
                data: "deadline",
            },
            {
                data: "status",
                render: (status) => {
                    let text;
                    switch (status) {
                        case "new":
                            text = "Baru";
                            break;
                        case "process":
                            text = "Diproses";
                            break;
                        case "done":
                            text = "Selesai";
                            break;
                        default:
                            text = "Diambil";
                            break;
                    }
                    return text;
                },
            },
            {
                data: "payment_status",
                render: (status) => {
                    let type;
                    let label;
                    switch (status) {
                        case "paid":
                            type = "success";
                            label = "Dibayar";
                            break;
                        default:
                            type = "warning";
                            label = "Belum Dibayar";
                            break;
                    }
                    return `<div class="badge badge-${type}">${label}</div>`;
                },
            },
            {
                width: "25%",
                data: "actions",
                searchable: false,
                sortable: false,
            },
        ],
    });

    $("#transactions-table").on("click", ".detail-button", async function () {
        let detailUrl = $(this).data("detail-url");
        try {
            let res = await fetchData(detailUrl);
            let transaction = res.transaction;
            let rows = "";

            transaction.details.forEach((item, index) => {
                rows += `<tr>
                            <td>${++index}</td>
                            <td>${item.service_name_history}</td>
                            <td>${item.price_history}</td>
                            <td>${item.qty}</td>
                            <td>${item.qty * item.price_history}</td>
                        </tr>`;
            });

            rows += `<tr>
                            <td colspan="4">Total Harga</td>
                            <td>${formatter.format(
                                transaction.total_price
                            )}</td>
                        </tr>`;

            if (transaction.payment_status == "paid") {
                rows += `<tr>
                            <td colspan="4">Diskon</td>
                            <td>${formatter.format(
                                transaction.total_discount
                            )}</td>
                        </tr>`;
                rows += `<tr>
                            <td colspan="4">Pajak</td>
                            <td>${formatter.format(transaction.total_tax)}</td>
                        </tr>`;
                rows += `<tr>
                            <td colspan="4">Biaya Tambahan</td>
                            <td>${formatter.format(
                                transaction.additional_cost
                            )}</td>
                        </tr>`;
                rows += `<tr>
                            <th colspan="4">Total</th>
                            <th>${formatter.format(
                                transaction.total_payment
                            )}</th>
                        </tr>`;
            }

            $("#transaction-info-table")
                .find(".td-transaction-invoice")
                .text(transaction.invoice);

            $("#transaction-info-table")
                .find(".td-transaction-member-name")
                .html(
                    `<label for="modal-tab-member" class="mb-0 text-primary">${transaction.member.name}</label>`
                );

            $("#transaction-info-table")
                .find(".td-transaction-date")
                .text(transaction.date);

            $("#transaction-info-table")
                .find(".td-transaction-deadline")
                .text(transaction.deadline);

            let paymentStatusType =
                transaction.payment_status == "paid" ? "success" : "secondary";
            let paymentStatusText =
                transaction.payment_status == "paid"
                    ? "Dibayar"
                    : "Belum dibayar";

            $("#transaction-info-table").find(
                ".td-transaction-payment-status"
            ).html(`<span class="font-weight-bold text-${paymentStatusType}">
                                ${paymentStatusText}
                            </span>`);

            $("#transaction-member-info")
                .find(".td-member-name")
                .text(transaction.member.name);

            $("#transaction-member-info")
                .find(".td-member-phone")
                .text(transaction.member.phone);

            $("#transaction-member-info")
                .find(".td-member-address")
                .text(transaction.member.address);

            $("#transaction-items-table tbody").html(rows);

            $("#transaction-detail-modal").find(
                "#button-container"
            ).html(`<a href="/o/${outletId}/transactions/${transaction.id}/invoice" class="btn btn-success w-100 mt-2 mb-0 print-invoice-button">
                                <i class="fas fa-print mr-1"></i>
                                <span>Cetak Faktur</span>
                            </a>`);

            $('[name="modal_tab"][value="items"]')
                .attr("checked", true)
                .trigger("change");
            $("#transaction-detail-modal").modal("show");
        } catch (err) {
            toast("error", "Terjadi kesalahan");
            $("#transaction-detail-modal").modal("hide");
        }
    });

    $("#transactions-table").on(
        "click",
        ".update-payment-button",
        async function () {
            let detailUrl = $(this).data("detail-url");
            let updateUrl = $(this).data("update-payment-url");

            try {
                let res = await fetchData(detailUrl);
                let transaction = res.transaction;
                let rows = "";

                transaction.details.forEach((item, index) => {
                    rows += `<tr>
                            <td>${++index}</td>
                            <td>${item.service_name_history}</td>
                            <td>${item.price_history}</td>
                            <td>${item.qty}</td>
                            <td>${item.qty * item.price_history}</td>
                        </tr>`;
                });

                if (lastPaymentDetail !== detailUrl) {
                    $("#update-payment-form").trigger("reset");
                    lastPaymentDetail = detailUrl;
                }

                $("#update-payment-form").attr("action", updateUrl);

                $("#payment-items-table tbody").html(rows);

                $("#payment-info-table")
                    .find(".td-transaction-invoice")
                    .text(transaction.invoice);

                $("#payment-info-table")
                    .find(".td-transaction-member-name")
                    .html(transaction.member.name);

                $("#payment-info-table")
                    .find(".td-transaction-date")
                    .text(transaction.date);

                $("#payment-info-table")
                    .find(".td-transaction-deadline")
                    .text(transaction.deadline);

                $("#payment-info-table")
                    .find(".td-transaction-payment-status")
                    .html(
                        transaction.payment_status == "paid"
                            ? "Dibayar"
                            : "Belum dibayar"
                    );

                $("#update-payment-form")
                    .find(".o-total-price")
                    .val(formatter.format(transaction.total_price));
                $("#update-payment-form")
                    .find(".o-total-payment")
                    .val(formatter.format(transaction.total_price));

                $("#update-payment-form")
                    .find(".int-total-price")
                    .val(transaction.total_price);

                $("#update-payment-modal").modal("show");
            } catch (err) {
                toast("error", "Terjadi kesalahan");
                $("#update-payment-modal").modal("hide");
            }
        }
    );

    $('[name="modal_tab"]').on("change", function () {
        if ($(this).val() == "member") {
            $(".nav-link.tab-member").addClass("active");
            $(".nav-link.tab-items").removeClass("active");
            $("#transaction-member-info").removeClass("d-none");
            $("#transaction-items-info").removeClass("d-block");
            $("#transaction-member-info").addClass("d-block");
            $("#transaction-items-info").addClass("d-none");
        } else {
            $(".nav-link.tab-member").removeClass("active");
            $(".nav-link.tab-items").addClass("active");
            $("#transaction-member-info").addClass("d-none");
            $("#transaction-member-info").removeClass("d-block");
            $("#transaction-items-info").addClass("d-block");
            $("#transaction-items-info").removeClass("d-none");
        }
    });

    $('[name="status_tab"]').on("change", function () {
        let status = $(this).val();
        $.each($(".nav-link-status"), (i, el) => {
            $(el).removeClass("active");
        });

        switch (status) {
            case "process":
                $(".nav-link.status-process").addClass("active");
                break;
            case "done":
                $(".nav-link.status-done").addClass("active");
                break;
            case "taken":
                $(".nav-link.status-taken").addClass("active");
                break;
            default:
                status = "new";
                $(".nav-link.status-new").addClass("active");
                break;
        }

        table.ajax.url(`${datatableUrl}?status=${status}`).load();
    });

    $("#transactions-table").on(
        "click",
        ".update-status-button",
        async function () {
            let message = "";
            let url = $(this).data("update-url");
            switch ($(this).data("status")) {
                case "new":
                    message = 'Ubah status cucian ke "Proses"?';
                    break;
                case "process":
                    message = 'Tandai cucian sebagai "Selesai"?';
                    break;
                case "done":
                    message = 'Tandai cucian sebagai "Sudah diambil"?';
                    break;
                default:
                    message = "";
            }
            let result = await Swal.fire({
                title: "Proses Transaksi",
                text: message,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Proses",
                cancelButtonText: "Batal",
            });
            if (result.isConfirmed) {
                try {
                    let res = await $.post(url, {
                        _token: $("[name=_token]").val(),
                        _method: "PUT",
                    });
                    toast(res.message, "success");
                    table.ajax.reload();
                } catch (err) {
                    toast("Terjadi kesalahan", "error");
                }
            }
        }
    );

    $("#update-payment-form").on("submit", async function () {
        event.preventDefault();
        let url = $(this).attr("action");
        let formData = $(this).serialize();
        try {
            let res = await $.post(url, formData);
            toast(res.message, "success");
            $("#update-payment-modal").modal("hide");
            table.ajax.reload();
        } catch (err) {
            if (err.status === 422)
                validationErrorHandler(err.responseJSON.errors);
            toast("Terjadi kesalahan", "error");
        }
    });

    $('[name="discount"]').on("change keydown", calculateTotalPayment);
    $('[name="discount_type"]').on("change", calculateTotalPayment);
    $('[name="tax"]').on("change keydown", calculateTotalPayment);
    $('[name="additional_cost"]').on("change keydown", calculateTotalPayment);
});

const calculateTotalPayment = () => {
    setTimeout(() => {
        let totalPrice = Number($(".int-total-price").val()) || 0;
        let tax = Number($('[name="tax"]').val()) || 0;
        let discount = Number($('[name="discount"]').val()) || 0;
        let additionalCost = Number($('[name="additional_cost"]').val()) || 0;
        let discountType = $('[name="discount_type"]').val();
        let totalTax = totalPrice * (tax / 100);
        let totalDiscount =
            discountType === "percent"
                ? totalPrice * (discount / 100)
                : discount;
        let totalPayment =
            totalPrice - totalDiscount + totalTax + additionalCost;
        $(".o-total-payment").val(formatter.format(totalPayment));
    });
};
