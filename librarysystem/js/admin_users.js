function showEditForm(userId) {
    var form = document.getElementById('edit-form-' + userId);
    var backdrop = document.querySelector('.backdrop');
    form.style.display = 'table-row';
    backdrop.style.display = 'block';
}

function hideEditForm(userId) {
    var openForms = document.querySelectorAll('.edit-form-row');
    openForms.forEach(function(form) {
        form.style.display = 'none';
    });
    document.querySelector('.backdrop').style.display = 'none';
}
