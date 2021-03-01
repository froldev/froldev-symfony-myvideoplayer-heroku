function onSearchVideo(event) {
    event.preventDefault();

	let value = document.querySelector("#search_video_search").value;

	if (value == "") {
		value = "all";
	}

	const entity = "video";
   	const url = "/"+entity+"/searchMovie/"+value;
    const tableContent = document.querySelector('tbody');

    axios.get(url).then(function(response) {

        tableContent.innerHTML = "";
		const items = response.data.videos;
		let content ="";

		if (items.length > 0) {
			items.forEach(function(item) {
				content = content + "<tr>";
				content = content + "<td>"+item.name+"</td>"
				content = content + "<td>"+item.category+"</td>"
				content = content + "<td>"+item.author+"</td>"
				content = content + "<td class='d-flex justify-content-evenly'>"
				content = content + "<a class='btn' href='/"+entity+"/"+item.slug+"/edit'><i class='fas fa-edit fa-lg text-success'></i></a>"
				content = content + "<a class='btn js-delete' href='/"+entity+"/"+item.slug+"/delete'><i class='fas fa-trash fa-lg text-danger'></i></a>"
				content = content + "</td>"
				content = content + "<tr>";
			});
		} else {
			content = content + "<tr>";
			content = content + "<td colspan='4'>Aucun résultat</td>"
			content = content + "<tr>";
		};
		tableContent.innerHTML = content;
    }); 
	
}

document.querySelector("#search_video_search").addEventListener("input", onSearchVideo);

document.querySelector("#search_video_search").addEventListener("keypress", (event) => {
	if (event.which === 13) {
		event.preventDefault;
	};
});
