let table;

$(function () {
    table = $("#items-table").DataTable({
        ...DATATABLE_OPTIONS,
        ajax: "/items/datatable",
        columns: [
            {
                data: "DT_RowIndex",
            },
            {
                data: "name",
            },
            {
                data: "qty",
            },
            {
                data: "price",
            },
            {
                data: "buy_date",
            },
            {
                data: "supplier",
            },
            {
                data: "update_status",
            },
            {
                data: "status_updated_at",
            },
            {
                data: "actions",
                searchable: false,
                sortable: false,
            },
        ],
    });

    // Event handlers
    $("#add-item-button").on("click", function () {
        let url = $(this).data("create-item-url");
        createHandler(url);
    });

    $("#items-table").on("click", ".edit-item-button", function () {
        let url = $(this).data("edit-item-url");
        editHandler(url);
    });

    $("#items-table").on("click", ".delete-item-button", function () {
        let url = $(this).data("delete-item-url");
        deleteHandler(url);
    });

    $("#item-form").on("submit", submitHandler);

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

    $("#items-table").on("change", "select.item-status", async function () {
        let url = $(this).data("update-url");
        try {
            let res = await $.post(url, {
                _token: $("[name=_token]").val(),
                _method: "PUT",
                status: $(this).val(),
            });
            toast(res.message, "success");
            // table.ajax.reload();
            let row = $(this).closest('tr');
            row.find('td:eq(7)').html(res.status_updated_at);
        } catch (err) {
            toast("Terjadi kesalahan", "error");
        }
    });

    $("#remove-import-file").on("click", removeImportFile);
});

const createHandler = (url) => {
    clearErrors();
    const modal = $("#form-modal");
    modal.modal("show");
    modal.find(".modal-title").text("Tambah Barang");
    modal.find("form")[0].reset();
    modal.find("form").attr("action", url);
    modal.find("[name=_method]").val("post");
};

const editHandler = async (url) => {
    clearErrors();
    const modal = $("#form-modal");
    modal.modal("show");
    modal.find(".modal-title").text("Edit Barang");
    modal.find("form")[0].reset();
    modal.find("form").attr("action", url);
    modal.find("[name=_method]").val("put");
    modal.find("input").attr("disabled", true);
    modal.find("select").attr("disabled", true);

    try {
        let res = await fetchData(url);
        modal.find("[name=name]").val(res.item.name);
        modal.find("[name=price]").val(res.item.price);
        modal.find("[name=qty]").val(res.item.qty);
        modal.find("[name=supplier]").val(res.item.supplier);
        modal.find("[name=buy_date]").val(res.item.buy_date);
        modal
            .find(`[name=status][value='${res.item.status}']`)
            .prop("checked", true);
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
        console.log(err.responseJSON);
        toast("Terjadi kesalahan", "error");
    }
};

const deleteHandler = async (url) => {
    let result = await Swal.fire({
        title: "Hapus barang",
        text: "Anda yakin ingin menghapus barang ini?",
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
