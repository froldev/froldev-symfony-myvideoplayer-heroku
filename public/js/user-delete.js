function onDeleteUser(event) {
	event.preventDefault();

    let confirmation = "Etes-vous sur de vouloir supprimer cet utilisateur ?";
    if (confirm(confirmation)){
        
        const url = this.href;
        let tableContent = document.querySelector('tbody');

        axios.get(url).then(function (response) {

            const items = response.data.users;
            tableContent.innerHTML = "";
            document.querySelector('#search_user_search').value = "";

            if (items.length > 0) {
                let content ="";
                items.forEach(function(item) {
                    content = content + "<tr>";
                        content = content + "<td>"+item.username+"</td>"
                        content = content + "<td>"+item.email+"</td>"
                        content = content + "<td class='d-flex justify-content-evenly'>"
                        content = content + "<a class='btn' href='/user/"+item.id+"/edit'><i class='fas fa-user-edit fa-lg text-success'></i></a>"
                        if (item.delete == true) {
                            content = content + "<a class='btn js-delete' href='/user/"+item.id+"/delete'><i class='fas fa-trash fa-lg text-danger'></i></a>"
                        }
                        content = content + "</td>"
                        content = content + "<tr>";
                });  
                tableContent.innerHTML = content;
            };
        }).catch(function(error) {
            window.alert('Il y a eu un problème lors de la suppression');
        });

    } else {
        return false;
    }
}
	
document.querySelectorAll('a.js-delete').forEach(function (link) {
	link.addEventListener('click', onDeleteUser);
})