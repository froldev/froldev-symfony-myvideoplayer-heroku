let div = document.querySelector(".alert");
if (div) {
    setTimeout(function () {
        div.animate({
        opacity: 0
        }, {
        duration: 1000,
        easing: "linear",
        iterations: 1,
        fill: "both"
        }).onfinish = function () {
        div.setAttribute('hidden', 'true');
        }
    }, 5000);
}
