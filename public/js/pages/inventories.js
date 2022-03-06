let table;

$(function () {
    table = $("#inventories-table").DataTable({
        ...DATATABLE_OPTIONS,
        ajax: "/inventories/datatable",
        columns: [
            {
                data: "DT_RowIndex",
            },
            {
                data: "name",
            },
            {
                data: "brand",
            },
            {
                data: "qty",
            },
            {
                data: "condition",
                render: (condition) => {
                    let text;
                    let type;
                    switch (condition) {
                        case "good":
                            text = "Bagus / layak pakai";
                            type = "success";
                            break;
                        case "damaged":
                            text = "Rusak ringan";
                            type = "warning";
                            break;
                        default:
                            text = "Rusak";
                            type = "danger";
                            break;
                    }
                    return `<span class="badge badge-${type}">${text}</span>`;
                },
            },
            {
                data: "procurement_date",
            },
            {
                data: "actions",
                searchable: false,
                sortable: false,
            },
        ],
    });
});

const createHandler = (url) => {
    clearErrors();
    const modal = $("#form-modal");
    modal.modal("show");
    modal.find(".modal-title").text("Tambah Barang Inventaris");
    modal.find("form")[0].reset();
    modal.find("form").attr("action", url);
    modal.find("[name=_method]").val("post");
};

const editHandler = async (url) => {
    clearErrors();
    const modal = $("#form-modal");
    modal.modal("show");
    modal.find(".modal-title").text("Edit Inventaris");
    modal.find("form")[0].reset();
    modal.find("form").attr("action", url);
    modal.find("[name=_method]").val("put");
    modal.find("input").attr("disabled", true);
    modal.find("select").attr("disabled", true);

    try {
        let res = await fetchData(url);
        modal.find("[name=name]").val(res.inventory.name);
        modal.find("[name=brand]").val(res.inventory.brand);
        modal.find("[name=qty]").val(res.inventory.qty);
        modal
            .find(`[name=condition][value='${res.inventory.condition}']`)
            .prop("checked", true);
        modal
            .find("[name=procurement_date]")
            .val(res.inventory.procurement_date);
    } catch (err) {
        console.log(err);
        toast("Tidak dapat mengambil data", "error");
    }

    modal.find("input").attr("disabled", false);
    modal.find("select").attr("disabled", false);
};

const submitHandler = async () => {
    event.preventDefault();
    const url = $("#form-modal form").attr("action");
    const formData = $("#form-modal form").serialize();
    try {
        let res = await $.post(url, formData);
        $("#form-modal").modal("hide");
        toast(res.message, "success");
        table.ajax.reload();
    } catch (err) {
        if (err.status === 422) validationErrorHandler(err.responseJSON.errors);
        toast("Terjadi kesalahan", "error");
    }
};

const deleteHandler = async (url) => {
    let result = await Swal.fire({
        title: "Hapus Outlet",
        text: "Anda yakin ingin menghapus outlet ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#6C757D",
        cancelButtonColor: "#037AFC",
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal",
    });
    if (result.isConfirmed) {
        try {
            let res = await $.post(url, {
                _token: $("[name=_token]").val(),
                _method: "delete",
            });
            toast(res.message, "success");
            table.ajax.reload();
        } catch (err) {
            toast("Terjadi kesalahan", "error");
        }
    }
};

const importHandler = async () => {
    event.preventDefault();
    let url = $("#import-form").attr("action");
    let formData = new FormData();
    formData.append("file_import", $("#file-import")[0].files[0]);
    $("#import-modal").modal("hide");
    try {
        let res = await $.ajax({
            method: "post",
            headers: {
                "X-CSRF-Token": $("[name=_token]").val(),
            },
            processData: false,
            contentType: false,
            cache: false,
            data: formData,
            enctype: "multipart/form-data",
            url: url,
        });
        toast(res.message, "success");
        table.ajax.reload();
        removeImportFile();
    } catch (err) {
        console.log(err);
        toast(
            err.status === 422 ? "File tidak valid" : "Terjadi kesalahan",
            "error"
        );
    }
};

const removeImportFile = () => {
    $("#file-import").val(null);
    $(".filename").val("");
    $(".filesize").val("");
    $("#import-file-card").addClass("d-none");
    $("#import-file-card").removeClass("d-flex");
    $("#select-import-file").removeClass("d-none");
    $("#select-import-file").addClass("d-block");
};

// Event handlers
$("#add-inventory-button").on("click", function () {
    let url = $(this).data("create-inventory-url");
    createHandler(url);
});

$("#inventories-table").on("click", ".edit-inventory-button", function () {
    let url = $(this).data("edit-inventory-url");
    editHandler(url);
});

$("#inventories-table").on("click", ".delete-inventory-button", function () {
    let url = $(this).data("delete-inventory-url");
    deleteHandler(url);
});

$("#inventory-form").on("submit", submitHandler);

$("#import-form").on("submit", importHandler);

$('[name="file_import"]').on("change", () => {
    let filename = $("#file-import")[0].files[0].name;
    let filesize = $("#file-import")[0].files[0].size;
    $(".filename").text(filename ?? "");
    $(".filesize").text(formatBytes(filesize) ?? "");
    $("#import-file-card").removeClass("d-none");
    $("#import-file-card").addClass("d-flex");
    $("#select-import-file").addClass("d-none");
    $("#select-import-file").removeClass("d-block");
});

$("#remove-import-file").on("click", removeImportFile);
