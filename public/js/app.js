const toaster = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
});

const formatter = Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    // These options are needed to round to whole numbers if that's what you want.
    //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
    //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
});

const toast = (message = "", type) => {
    Swal.fire({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        title: `&nbsp;&nbsp;${message}`,
        icon: type,
    });
};

const formatBytes = (bytes, decimals = 2) => {
    if (bytes === 0) return "0 Bytes";

    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ["Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];

    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + " " + sizes[i];
};

const validationErrorHandler = (errors) => {
    clearErrors();
    for (let name in errors) {
        let container = $(`[name=${name}]`).closest(".form-group").eq(0);
        container.append(
            `<div class="small form-errors text-danger">${errors[name][0]}</div>`
        );
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
