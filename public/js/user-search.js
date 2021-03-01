function onSearchUser(event) {
    event.preventDefault();

	let value = document.querySelector("#search_user_search").value;

	if (value == "") {
		value = "all";
	}

	const entity = "user";
   	const url = "/"+entity+"/searchUser/"+value;
    const tableContent = document.querySelector('tbody');

    axios.get(url).then(function(response) {

        tableContent.innerHTML = "";
		const items = response.data.users;
		let content ="";

		if (items.length > 0) {
			items.forEach(function(item) {
				content = content + "<tr>";
				content = content + "<td>"+item.username+"</td>"
				content = content + "<td>"+item.email+"</td>"
				content = content + "<td class='d-flex justify-content-evenly'>"
				content = content + "<a class='btn' href='/"+entity+"/"+item.id+"/edit'><i class='fas fa-user-edit fa-lg text-success'></i></a>"
				if (item.delete == true) {
					content = content + "<a class='btn js-delete' href='/"+entity+"/"+item.id+"/delete'><i class='fas fa-trash fa-lg text-danger'></i></a>"
				}
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

document.querySelector("#search_user_search").addEventListener("input", onSearchUser);

document.querySelector("#search_user_search").addEventListener("keypress", (event) => {
	if (event.which === 13) {
		event.preventDefault;
	};
});
