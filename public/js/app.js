const toaster = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
});

const toast = (message = "", type) => {
    Swal.fire({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        title: message,
        icon: type,
    });
};

const validationErrorHandler = (errors) => {
    clearErrors();
    for (let name in errors) {
        let container = $(`[name=${name}]`).closest(".form-group").eq(0);
        container.append( `<div class="small form-errors text-danger">${errors[name][0]}</div>`);
    }
};

const clearErrors = () => {
    $(".form-errors").remove();
};

const logoutHandler = async () => {
    let result = await Swal.fire({
        title: "Logout?",
        text: "Anda yakin ingin logout?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#6C757D",
        cancelButtonColor: "#4DA3B8",
        confirmButtonText: "Logout",
    });
    if (result.isConfirmed) {
        $("#logoutForm").submit();
    }
};

const fetchData = async (url) => {
    return new Promise(async (resolve, reject) => {
        try {
            let res = await $.get(url);
            resolve(res);
        } catch (err) {
            reject(err);
        }
    });
};
