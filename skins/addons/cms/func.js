function manageTypeField(selectImageContainerId, textContainerId, type) {
  if (selectImageContainerId == '' || textContainerId == '' || type == '') return;
  var selectImageContainer = document.getElementById(selectImageContainerId);
  var textContainer = document.getElementById(textContainerId);
  if (selectImageContainer && textContainer) {
    switch (type) {
      case 'image':
        selectImageContainer.style.display = 'block';
        textContainer.style.display = 'none';
        break;
      case 'html':
        selectImageContainer.style.display = 'none';
        textContainer.style.display = 'block';
        break;
    }
  }
}

  function resetFilter() {
     document.filterBannersForm.reset();
  }
