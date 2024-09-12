//no

function addAddress() {
    const addressContainer = document.getElementById('address_container');
    const addressDiv = document.createElement('div');
    addressDiv.classList.add('work_city_div');

    const midPointLabel = document.createElement('a');
    midPointLabel.classList.add('work_word');
    midPointLabel.innerText = '中途點：';
    addressDiv.appendChild(midPointLabel);

    const deleteButton = document.createElement('button');
    deleteButton.classList.add('work_address_delete');
    deleteButton.setAttribute('type', 'button');
    deleteButton.setAttribute('onclick', 'removeAddress(this);');
    deleteButton.innerText = '刪除';
    addressDiv.appendChild(deleteButton);

    addressDiv.appendChild(document.createElement('br'));
    addressDiv.appendChild(document.createElement('br'));

    const horizontalDiv1 = document.createElement('div');
    horizontalDiv1.id = '水平靠左';

    horizontalDiv1.appendChild(document.createTextNode('\u00A0\u00A0\u00A0\u00A0'));

    const cityLabel = document.createElement('label');
    cityLabel.setAttribute('for', 'address_city');
    cityLabel.innerText = '城市：';
    horizontalDiv1.appendChild(cityLabel);

    const citySelect = document.createElement('select');
    citySelect.classList.add('work_city');
    citySelect.setAttribute('id', 'city_list');
    citySelect.setAttribute('name', 'city');
    citySelect.setAttribute('onChange', 'getArea(this.value);');
    citySelect.required = true;

    citySelect.innerHTML = `
        <option value disabled selected>請選擇城市</option>
        <?php foreach($results as $city){ ?>
        <option value="<?php echo $city['city_id']; ?>"><?php echo $city['city_name']; ?></option>
        <?php } ?>
    `;

    horizontalDiv1.appendChild(citySelect);
    horizontalDiv1.appendChild(document.createTextNode('\u00A0\u00A0\u00A0\u00A0\u00A0\u00A0'));

    const areaLabel = document.createElement('label');
    areaLabel.setAttribute('for', 'address_area');
    areaLabel.innerText = '鄉鎮區：';
    horizontalDiv1.appendChild(areaLabel);

    const areaSelect = document.createElement('select');
    areaSelect.classList.add('work_city');
    citySelect.setAttribute('id', 'area_list');
    areaSelect.setAttribute('name', 'area');
    areaSelect.required = true;
    areaSelect.innerHTML = '<option value="">請選擇鄉鎮區</option>';
    horizontalDiv1.appendChild(areaSelect);

    addressDiv.appendChild(horizontalDiv1);
    addressDiv.appendChild(document.createElement('br'));

    const horizontalDiv2 = document.createElement('div');
    horizontalDiv2.id = '水平靠左';

    horizontalDiv2.appendChild(document.createTextNode('\u00A0\u00A0\u00A0\u00A0'));

    const detailLabel = document.createElement('label');
    detailLabel.setAttribute('for', 'address_detail');
    detailLabel.innerText = '詳細地址：';
    horizontalDiv2.appendChild(detailLabel);

    horizontalDiv2.appendChild(document.createTextNode('\u00A0'));

    const detailInput = document.createElement('input');
    detailInput.classList.add('work_address_detail');
    detailInput.setAttribute('type', 'text');
    detailInput.setAttribute('id', 'address_detail');
    detailInput.setAttribute('name', 'address_detail');
    detailInput.required = true;
    horizontalDiv2.appendChild(detailInput);

    addressDiv.appendChild(horizontalDiv2);
    addressContainer.appendChild(addressDiv);
}

function removeAddress(button) {
    const addressDiv = button.parentElement;
    addressDiv.remove();
}

// function getArea(selectElement) {
//     const addressDiv = selectElement.closest('.work_city_div');
//     const areaSelect = addressDiv.querySelector('select[name="area[]"]');
//     const cityId = selectElement.value;

//     $.ajax({
//         type: "POST",
//         url: "getArea.php",
//         data: "city_id=" + cityId,
//         success: function(data) {
//             areaSelect.innerHTML = data;
//         }
//     });
// }
