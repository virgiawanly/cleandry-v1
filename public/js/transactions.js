let membersTable;
let itemCounter = 1;

$(function () {
    const membersTableOptions = {
        responsive: true,
        ajax: {
            url: "/members/datatable",
        },
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
                render: function (data) {
                    return `<button type="button" data-dismiss="modal" class="btn btn-success" onclick="loadMemberDetail(${data})">
                                <i class="fas fa-check-circle mr-1"></i>
                                <span>Pilih</span>
                            </button>`;
                },
            },
        ],
    };
    membersTable = $("#membersTable").DataTable(membersTableOptions);
    //Initialize Select2 Elements
    $(".select2bs4").select2({
        theme: "bootstrap4",
    });
});

const loadServiceDetail = function (serviceId) {
    const url = `/services/${serviceId}`;
    const form = $("form#addItemForm");
    const qty = form.find(".input-qty").val();
    form.find(".input-service-type").val("-");
    form.find(".input-service-price").val("0");
    form.find(".input-total-price").val("0");
    form.find("#addItemBtn").attr("disabled", true);
    $.get(url)
        .done((res) => {
            const service = res.service;
            let total = qty == NaN ? 0 : qty * service.price;
            form.find(".input-service-type").val(service.type.name);
            form.find(".input-service-unit").val(service.unit);
            form.find(".input-service-price").val(service.price);
            form.find(".input-total-price").val(total);
            form.find("#addItemBtn").attr("disabled", false);
        })
        .fail((err) => {
            Toast.fire({
                icon: "error",
                title: "Tidak dapat mengambil data",
            });
            return;
        });
};

const addItemHandler = function () {
    event.preventDefault();
    const form = $("form#addItemForm");
    const serviceId = form.find(".input-service-id").val();
    const serviceName = form.find(".input-service-id option:selected").text();
    const qty = form.find(".input-qty").val();
    const servicePrice = form.find(".input-service-price").val();
    const totalPrice = form.find(".input-total-price").val();
    if (
        !serviceId ||
        serviceId == "" ||
        qty == NaN ||
        qty == "" ||
        qty < 1 ||
        servicePrice == "" ||
        totalPrice == ""
    )
        return;
    renderItem({
        serviceId,
        serviceName,
        qty,
        servicePrice,
        totalPrice,
    });
    $("#addItemContainer").find("input").val("");
};

const renderItem = function (item) {
    const container = $("#itemsContainer");
    const el = `<!-- Single item -->
                <tr class="single-item">
                    <td>
                        <span>${itemCounter}</span>
                        <input type="hidden" name="serviceId[]" value="${item.serviceId}">
                    </td>
                    <td>${item.serviceName}</td>
                    <td>${item.servicePrice}</td>
                    <td>
                        <span>${item.qty}</span>
                        <input type="hidden" name="qty[]" value="${item.qty}">
                    </td>
                    <td>${item.totalPrice}</td>
                    <td><textarea name="description[]" cols="10" rows="2" placeholder="Tambahkan catatan" class="form-control"></textarea></td>
                    <td>
                        <button class="btn btn-danger" onclick="removeItem(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
    itemCounter++;
    container.append(el);
};

const updateTotalPrice = function () {
    setTimeout(() => {
        const form = $("form#addItemForm");
        let kuantitas = form.find(".input-qty").val();
        let harga = form.find(".input-service-price").val();
        let total = kuantitas * harga;
        form.find(".input-total-price").val(total);
    });
};

const removeItem = function (el) {
    const item = el.closest("tr.single-item");
    item.remove();
};

const loadMemberDetail = function (memberId) {
    const url = `/members/${memberId}`;
    const form = $("form#transactionForm");
    form.find('[name="member_id"]').val(memberId);
    $.get(url)
        .done((res) => {
            const member = res.member;
            form.find(".info-member-name").text(member.name);
            form.find(".info-member-phone").text(member.phone);
            form.find(".info-member-address").text(member.address);
        })
        .fail((err) => {
            Toast.fire({
                icon: "error",
                title: "Tidak dapat mengambil data",
            });
            return;
        });
};

const syncMembers = function () {
    membersTable.ajax.reload();
};
