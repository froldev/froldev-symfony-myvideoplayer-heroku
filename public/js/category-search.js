function onSearchCategory(event) {
    event.preventDefault();

	let value = document.querySelector("#search_category_search").value;

	if (value == "") {
		value = "all";
	}

	const entity = "category";
   	const url = "/"+entity+"/searchCategory/"+value;
    const tableContent = document.querySelector('tbody');

    axios.get(url).then(function(response) {

        tableContent.innerHTML = "";
		const items = response.data.categories;
		let content ="";

		if (items.length > 0) {
			items.forEach(function(item) {
				content = content + "<tr>";
				content = content + "<td>"+item.name+"</td>"
				content = content + "<td>"+item.position+"</td>"
				content = content + "<td class='d-flex justify-content-evenly'>"
				content = content + "<a class='btn' href='/"+entity+"/"+item.slug+"/edit'><i class='fas fa-edit fa-lg text-success'></i></a>"
				content = content + "<a class='btn js-delete' href='/"+entity+"/"+item.slug+"/delete'><i class='fas fa-trash fa-lg text-danger'></i></a>"
				content = content + "</td>"
				content = content + "<tr>";
			});
		} else {
			content = content + "<tr>";
			content = content + "<td colspan='3'>Aucun résultat</td>"
			content = content + "<tr>";
		};
		tableContent.innerHTML = content;
    }).catch(function(error) {
		window.alert('Il y a eu un problème lors de la recherche');
	});
}

document.querySelector("#search_category_search").addEventListener("input", onSearchCategory);

document.querySelector("#search_category_search").addEventListener("keypress", (event) => {
	if (event.which === 13) {
		event.preventDefault;
	};
});
