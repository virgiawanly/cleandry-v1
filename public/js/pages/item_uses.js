let table;

$(function () {
    // Initialize Datatable
    table = $("#items-table").DataTable({
        responsive: true,
        pageLength: 25,
        ajax: "/uses/datatable",
        columns: [
            {
                data: "DT_RowIndex",
            },
            {
                data: "item_name",
            },
            {
                data: "start_use",
            },
            {
                data: "end_use",
                render: (endUse) => endUse ?? "-",
            },
            {
                data: "user_name",
            },
            {
                data: "update_status",
            },
            {
                data: "actions",
                searchable: false,
                sortable: false,
            },
        ],
    });

    // Event ketika tombol tambah data diklik
    $("#add-item-button").on("click", function () {
        let url = $(this).data("create-item-url");
        createHandler(url);
    });

    // Event ketika select status di modal diubah
    $('[name="status"]').on("change", function () {
        let status = $(this).val();
        if (status === "in_use") {
            $('[name="end_use"]').val("");
            $('[name="end_use"]').attr("disabled", true);
        } else {
            $('[name="end_use"]').attr("disabled", false);
        }
    });

    $("#items-table").on("click", ".edit-item-button", function () {
        let url = $(this).data("edit-url");
        editHandler(url);
    });

    $("#items-table").on("click", ".delete-item-button", function () {
        let url = $(this).data("delete-url");
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
            let row = $(this).closest("tr");
            if (res.end_use) {
                row.find("td:eq(3)").html(res.end_use);
            } else {
                row.find("td:eq(3)").html("-");
            }
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
    modal.find(".modal-title").text("Tambah Data Penggunaan Barang");
    modal.find("form")[0].reset();
    modal.find("form").attr("action", url);
    modal.find("[name=_method]").val("post");
};

const editHandler = async (url) => {
    clearErrors();
    const modal = $("#form-modal");
    modal.modal("show");
    modal.find(".modal-title").text("Edit Data Penggunaan Barang");
    modal.find("form")[0].reset();
    modal.find("form").attr("action", url);
    modal.find("[name=_method]").val("put");
    modal.find("input").attr("disabled", true);
    modal.find("select").attr("disabled", true);

    try {
        let res = await fetchData(url);
        console.log(res);
        modal.find("[name=item_name]").val(res.itemUses.item_name);
        modal.find("[name=user_name]").val(res.itemUses.user_name);
        modal.find("[name=start_use]").val(res.itemUses.start_use);
        if (res.itemUses.end_use) {
            modal.find("[name=end_use]").attr("disabled", false);
            modal.find("[name=end_use]").val(res.itemUses.end_use);
        } else {
            modal.find("[name=end_use]").attr("disabled", true);
            modal.find("[name=end_use]").val("");
        }
        modal
            .find(`[name=status][value='${res.itemUses.status}']`)
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
        toast("Terjadi kesalahan", "error");
    }
};

const deleteHandler = async (url) => {
    let result = await Swal.fire({
        title: "Hapus data",
        text: "Anda yakin ingin menghapus data penggunaan ini?",
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
