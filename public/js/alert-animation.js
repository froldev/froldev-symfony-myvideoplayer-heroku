<script>
	const div = document.querySelector(".alert");
    setTimeout(() => {
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
</script>