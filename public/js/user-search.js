function onSearchUser(event) {
    event.preventDefault();

	let value = document.querySelector("#search_user_search").value;

	if (value == "") {
		value = "all";
	}

   	const url = "/user/searchUser/"+value;
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
				content = content + "<a class='btn' href='/video/"+item.id+"/edit'><i class='fas fa-user-edit fa-lg text-success'></i></a>"
				if (item.delete == true) {
					content = content + "<button class='btn' href='/video/"+item.id+"/delete'><i class='fas fa-trash fa-lg text-danger'></i></a>"
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
    }); 
	
}

document.querySelector("#search_user_search").addEventListener("input", onSearchUser);

document.querySelector("#search_user_search").addEventListener("keypress", (event) => {
	if (event.which === 13) {
		event.preventDefault;
	};
});
