document.addEventListener('DOMContentLoaded', function () {
    var editButton = document.getElementById('editButton');
    var editSection = document.querySelector('.edit-section');
    const nonHidden = document.querySelector('.non-hidden'); 
  
    function toggleEditSection() {
      editSection.classList.toggle('hidden');
      nonHidden.classList.toggle('hidden');
      editButton.classList.add('hidden');
    }
  
    if (editButton) {
      editButton.addEventListener('click', function() {
        toggleEditSection();
      });
    } else {
      console.error('Edit button not found.');
    }
  });