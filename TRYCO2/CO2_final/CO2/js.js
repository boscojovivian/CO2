//回頂端
window.addEventListener('scroll', function () {
    var scrollPosition = window.scrollY;

    if (scrollPosition > 200) { // 調整這個閾值
        // 顯示回到頂部按鈕的代碼
        document.querySelector('.back-to-top').style.display = 'block';
    } else {
        // 隱藏回到頂部按鈕的代碼
        document.querySelector('.back-to-top').style.display = 'none';
    }
});


//回報問題視窗
function openContactForm() {
    document.getElementById('contactForm').style.display = 'block';
}
function closeContactForm() {
    document.getElementById('contactForm').style.display = 'none';
}
function ContactFormSuccess(){
	alert("送出成功！");
}


//員工新增地址
// document.getElementById('city').addEventListener('change', function() {
//     var city = this.value;
//     var xhr = new XMLHttpRequest();
//     xhr.open('GET', 'get_districts.php?city=' + city, true);
//     xhr.onload = function() {
//         if (xhr.status == 200) {
//             var districts = JSON.parse(xhr.responseText);
//             var districtSelect = document.getElementById('district');
//             districtSelect.innerHTML = '<option value="">請選擇鄉鎮區</option>';
//             districts.forEach(function(district) {
//                 var option = document.createElement('option');
//                 option.text = district;
//                 option.value = district;
//                 districtSelect.add(option);
//             });
//         }
//     };
//     xhr.send();
// });

//下拉式選單
var VisibleMenu = ''; // 記錄目前顯示的子選單的 ID

// 顯示或隱藏子選單
function switchMenu( theMainMenu, theSubMenu, theEvent ){
    var SubMenu = document.getElementById( theSubMenu );
    if( SubMenu.style.display == 'none' ){ // 顯示子選單
        SubMenu.style.minWidth = theMainMenu.clientWidth; // 讓子選單的最小寬度與主選單相同 (僅為了美觀)
        SubMenu.style.display = 'block';
        hideMenu(); // 隱藏子選單
        VisibleMenu = theSubMenu;
    }
    else{ // 隱藏子選單
        if( theEvent != 'MouseOver' || VisibleMenu != theSubMenu ){
            SubMenu.style.display = 'none';
            VisibleMenu = '';
        }
    }
}

// 隱藏子選單
function hideMenu(){
    if( VisibleMenu != '' ){
        document.getElementById( VisibleMenu ).style.display = 'none';
    }
    VisibleMenu = '';
}
