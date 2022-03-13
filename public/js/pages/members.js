let table;

$(function () {
    const outletId = $("meta[name='outlet-id']").attr("content");
    table = $("#members-table").DataTable({
        ...DATATABLE_OPTIONS,
        ajax: `/o/${outletId}/members/datatable`,
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
                data: "email",
            },
            {
                data: "gender",
                render: (gender) => (gender == "M" ? "Laki-laki" : "Perempuan"),
            },
            {
                data: "address",
            },
            {
                data: "actions",
                searchable: false,
                sortable: false,
            },
        ],
    });

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
});

const createHandler = (url) => {
    clearErrors();
    const modal = $("#form-modal");
    modal.modal("show");
    modal.find(".modal-title").text("Register Member Baru");
    modal.find("form")[0].reset();
    modal.find("form").attr("action", url);
    modal.find("[name=_method]").val("post");
};

const editHandler = async (url) => {
    clearErrors();
    const modal = $("#form-modal");
    modal.modal("show");
    modal.find(".modal-title").text("Edit member");
    modal.find("form")[0].reset();
    modal.find("form").attr("action", url);
    modal.find("[name=_method]").val("put");
    modal.find("input").attr("disabled", true);

    try {
        let res = await fetchData(url);
        modal.find("[name=name]").val(res.member.name);
        modal.find("[name=phone]").val(res.member.phone);
        modal.find("[name=email]").val(res.member.email);
        modal.find("[name=address]").val(res.member.address);
        modal
            .find(`[name=gender][value='${res.member.gender}']`)
            .prop("checked", true);
    } catch (err) {
        toast("Tidak dapat mengambil data", "error");
        $("#form-modal").modal("hide");
    }

    modal.find("input").attr("disabled", false);
};

const submitHandler = async () => {
    event.preventDefault();
    let url = $("#form-modal form").attr("action");
    let formData = $("#form-modal form").serialize();
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
        title: "Hapus Member",
        text: "Anda yakin ingin menghapus member ini?",
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
        console.log(err.responseJSON);
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
