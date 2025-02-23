$(document).ready(function() {
    $('.sidebar-toggle').click(function() {
        $('.sidebar').toggleClass('open');
        $('.main-content').toggleClass('shift');
    });

    if ($(window).width() >= 768) {
        $('.sidebar').addClass('open');
        $('.main-content').addClass('shift');
    }

    $(document).click(function(event) {
        if (!$(event.target).closest('.sidebar, .sidebar-toggle').length) {
            $('.sidebar').removeClass('open');
            $('.main-content').removeClass('shift');
        }
    });

    $('#select-all').change(function() {
        $('.product-checkbox, .order-checkbox, .tag-checkbox').prop('checked', this.checked);
    });
});

function deleteImage(imageId) {
    if (confirm('Вы уверены, что хотите удалить это изображение?')) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'images_to_delete[]';
        input.value = imageId;
        document.querySelector('form').appendChild(input);

        document.getElementById(imageId).parentElement.remove();
    }
}

function previewMainImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('mainImagePreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function handleAdditionalImageUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    const container = document.getElementById('additionalImagesContainer');
    const input = event.target;

    const previewDiv = document.createElement('div');
    previewDiv.className = 'mb-2 d-flex align-items-center';

    const img = document.createElement('img');
    img.src = URL.createObjectURL(file);
    img.alt = 'Дополнительное изображение';
    img.className = 'img-thumbnail me-2';
    img.style = 'width: 150px; height: auto;';

    // Кнопка для отмены добавления
    const cancelButton = document.createElement('button');
    cancelButton.type = 'button';
    cancelButton.className = 'btn btn-danger btn-sm';
    cancelButton.textContent = 'Отменить';
    cancelButton.onclick = function() {
        container.removeChild(previewDiv);
        input.remove();
    };

    previewDiv.appendChild(img);
    previewDiv.appendChild(cancelButton);
    container.appendChild(previewDiv);

    input.parentElement.style.display = 'none';

    const newInputContainer = document.createElement('div');
    newInputContainer.className = 'additional-image-input-container mt-2';

    const newInput = document.createElement('input');
    newInput.type = 'file';
    newInput.className = 'additional-image-input';
    newInput.name = 'additional_images[]';
    newInput.accept = 'image/*';
    newInput.onchange = handleAdditionalImageUpload;

    const newInputLabel = document.createElement('label');
    newInputLabel.htmlFor = newInput.id;
    newInputLabel.className = 'additional-image-input-label';
    newInputLabel.innerHTML = '<i class="fas fa-image"></i> + Добавить изображение';

    newInputContainer.appendChild(newInput);
    newInputContainer.appendChild(newInputLabel);
    container.appendChild(newInputContainer);
}

const tagsContainer = document.getElementById('tags-container');

tagsContainer.addEventListener('click', function(event) {
    const tag = event.target.closest('.tag');
    if (tag) {
        const checkbox = tag.querySelector('input[type="checkbox"]');
        checkbox.checked = !checkbox.checked;
        tag.classList.toggle('selected', checkbox.checked);
    }
});

function initProductForm() {
    const mainImageInput = document.getElementById('main_image');
    const mainImagePreview = document.getElementById('main-image-preview');
    setupMainImageInput(mainImageInput, mainImagePreview);

    const additionalImagesInput = document.getElementById('additional_images');
    const additionalImagesPreview = document.getElementById('additional-images-preview');
    setupAdditionalImagesInput(additionalImagesInput, additionalImagesPreview);
}

function setupMainImageInput(input, previewContainer) {
    input.addEventListener('change', function(event) {
        const file = event.target.files[0];
        previewContainer.innerHTML = '';
        if (file) {
            showPreview(file, previewContainer, true);
        }
    });
}

function setupAdditionalImagesInput(input, previewContainer) {
    input.addEventListener('change', function(event) {
        previewContainer.innerHTML = '';
        Array.from(event.target.files).forEach(file => {
            showPreview(file, previewContainer, false);
        });
    });
}

function showPreview(file, container, isMain) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const previewItem = createPreviewItem(e.target.result, isMain, file);
        container.appendChild(previewItem);
    };
    reader.readAsDataURL(file);
}

function createPreviewItem(imageSrc, isMain, file) {
    const previewItem = document.createElement('div');
    previewItem.className = 'preview-item';

    const img = document.createElement('img');
    img.src = imageSrc;
    previewItem.appendChild(img);

    const removeButton = document.createElement('button');
    removeButton.className = 'preview-item-remove';
    removeButton.textContent = '×';
    removeButton.addEventListener('click', function() {
        previewItem.remove();
        if (isMain) {
            document.getElementById('main_image').value = '';
        } else {
            removeFileFromInput(file);
        }
    });
    previewItem.appendChild(removeButton);

    return previewItem;
}

function removeFileFromInput(fileToRemove) {
    const input = document.getElementById('additional_images');
    const files = Array.from(input.files);
    const remainingFiles = files.filter(file => file !== fileToRemove);

    const dataTransfer = new DataTransfer();
    remainingFiles.forEach(file => dataTransfer.items.add(file));
    input.files = dataTransfer.files;
}
