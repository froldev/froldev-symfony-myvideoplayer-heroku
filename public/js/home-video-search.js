function onSearchVideo(event) {
    event.preventDefault();

	let value = document.querySelector("#search_video_search").value;

	if (value == "") {
		value = "all";
	}

   	const url = "/searchBestMovie/"+value;
    const listOfVideos = document.querySelector('#videos');
	const maxLength = 62;

    axios.get(url).then(function(response) {

        listOfVideos.innerHTML = "";
		const items = response.data.videos;
		let content = "";

		console.log(items);

		if (document.querySelector("#search_video_search").value == "") {
			content = content + "<h2>Les meilleures vidéos : "+items.length+" vidéos</h2>";
		} else {
			if (items.length == 0) {
				content = content + "<h2>Aucun résultat </h2>"
			} else if (items.length > 1) {
				content = content + "<h2>"+items.length+" résultats : </h2>";
			} else {
				content = content + "<h2>"+items.length+" résultat : </h2>";
			}
		}
		content = content + "<div class='row row-cols-1 row-cols-md-3 g-2'>";

		if (items.length > 0)  {
			items.forEach(function(item) {
				content = content + "<div class='col'>";
				content = content + "<div class='card shadow p-3 mb-5 bg-white rounded'>"
				content = content + "<a href='/video/"+item.slug+"'>"
				content = content + "<img src='"+scraptUrl(item.url)+"' height='222' class='card-img-top' alt='"+item.title+"' />"
				content = content + "</a>"
				content = content + "<div class='card-body'>"
				if (item.title.length > maxLength) {
					item.title.substring(0, maxLength) + "...";
				}
				content = content + "<h5 class='card-title' style='height: 48px;'>"+item.title+"</h5>"
				content = content + "</div></div></div>"				
			});
		}
		content = content + "</div>";
		listOfVideos.innerHTML = content;
    }); 
	
}

function scraptUrl(url)
{
	let id = url.split('v=')[1];
	let ampersandPosition = id.indexOf('&');
	if(ampersandPosition != -1) {
	id = id.substring(0, ampersandPosition);
	};
	return "https://img.youtube.com/vi/"+id+"/maxresdefault.jpg";
}

document.querySelector("#search_video_search").addEventListener("input", onSearchVideo);

document.querySelector("#search_video_search").addEventListener("keypress", (event) => {
	if (event.which === 13) {
		event.preventDefault;
	};
});
