function onDeleteCategory(event) {
	event.preventDefault();

    let confirmation = "Etes-vous sur de vouloir supprimer cette catégorie ?";
    if (confirm(confirmation)){
        
        const url = this.href;
        let tableContent = document.querySelector('tbody');

        axios.get(url).then(function (response) {

            const items = response.data.categories;
            tableContent.innerHTML = "";

            if (items.length > 0) {
                let content ="";
                items.forEach(function(item) {
                    content = content + "<tr>";
                    content = content + "<td>"+item.name+"</td>"
                    content = content + "<td>"+item.position+"</td>"
                    content = content + "<td class='d-flex justify-content-evenly'>"
                    content = content + "<a class='btn' href='/video/"+item.slug+"/edit'><i class='fas fa-edit fa-lg text-success'></i></a>"
                    content = content + "<a class='btn js-delete' href='/video/"+item.slug+"/delete'><i class='fas fa-trash fa-lg text-danger'></i></a>"
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
	link.addEventListener('click', onDeleteCategory);
})