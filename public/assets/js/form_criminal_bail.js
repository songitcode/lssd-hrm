let mucAn = 0; // Tổng điểm
const giaTien1Phut = 3000; // Giá mỗi điểm

// Hàm xử lý chọn và bỏ chọn các thẻ
function selectSuggestion(element, points, inputId, inputIdPoints, inputIdCost) {
    const inputField = document.getElementById(inputId);
    const inputFieldPoints = document.getElementById(inputIdPoints);
    const inputFieldCost = document.getElementById(inputIdCost);

    // Lấy giá trị hiện tại trong ô input và nội dung gợi ý
    const newValue = element.textContent.trim();
    const currentValues = inputField.value.split(" + ").map(value => value.trim());

    // Kiểm tra nếu giá trị đã tồn tại
    if (currentValues.includes(newValue)) {
        // Nếu giá trị tồn tại, xóa nó và trừ điểm
        inputField.value = currentValues.filter(value => value !== newValue).join(" + ");
        mucAn -= points; // Trừ điểm
        element.classList.remove("selected"); // Bỏ trạng thái chọn
    } else {
        // Nếu giá trị chưa tồn tại, thêm nó và cộng điểm
        if (inputField.value) {
            inputField.value += ` + ${newValue}`;
        } else {
            inputField.value = newValue;
        }
        mucAn += points; // Cộng điểm
        element.classList.add("selected"); // Thêm trạng thái chọn
    }

    // Cập nhật giá trị vào các ô input
    const tongTienBaoLanh = mucAn * giaTien1Phut;
    inputFieldPoints.value = `${mucAn}p`;
    inputFieldCost.value = `${tongTienBaoLanh.toLocaleString()}$`;

    // Xử lý chọn và bỏ chọn giữa not-qs và yet-qs
    // if (element.id === 'not-qs') {
    //     // Nếu chọn not-qs thì bỏ chọn yet-qs
    //     const yetQs = document.getElementById('yet-qs');
    //     if (yetQs.classList.contains('selected')) {
    //         yetQs.classList.remove('selected');
    //         yetQs.style.background = ''; // Reset background    
    //     }
    // } else if (element.id === 'yet-qs') {
    //     // Nếu chọn yet-qs thì bỏ chọn not-qs
    //     const notQs = document.getElementById('not-qs');
    //     if (notQs.classList.contains('selected')) {
    //         notQs.classList.remove('selected');
    //         notQs.style.background = ''; // Reset background
    //     }
    // }
}

// Hàm sao chép dữ liệu vào clipboard
function copyAllToClipboard() {
    // Lấy giá trị từ các ô input
    const tenNguoiGiaiQuyet = document.getElementById('input-1').value;
    const tenNguoiBaoLanh = document.getElementById('input-2').value;
    const tenNguoiViPham = document.getElementById('input-3').value;
    const cccd = document.getElementById('input-4').value;
    const toiDanh = document.getElementById('input-5').value;
    const mucAn = document.getElementById('input-6').value;
    const soTien = document.getElementById('input-7').value;

    // Tạo chuỗi văn bản theo định dạng yêu cầu
    const resultText =
        `Tên Người Giải Quyết: ${tenNguoiGiaiQuyet}
Tên Người Bảo Lãnh: ${tenNguoiBaoLanh}
Tên Người Vi Phạm: ${tenNguoiViPham}
CCCD: ${cccd}
Tội danh: ${toiDanh}
Mức án: ${mucAn}
Số tiền: ${soTien}
Đã xử lý`;

    // Tạo một textarea ẩn để sao chép văn bản vào clipboard
    const textarea = document.createElement('textarea');
    textarea.value = resultText;
    document.body.appendChild(textarea);

    // Chọn và sao chép văn bản
    textarea.select();
    document.execCommand('copy');

    // Xóa textarea sau khi sao chép
    document.body.removeChild(textarea);

    // Thông báo cho người dùng
    alert('Dữ liệu đã được sao chép!\n' + resultText);

    // Tính tiền cho input-7 tự động
    tuDongTinhTien();
}

// Hàm tính tiền tự động cho input-7 dựa trên mức án
function tuDongTinhTien() {
    const inputMucAn = document.getElementById('input-6'); // Mức án
    const inputTien = document.getElementById('input-7'); // Số tiền

    // Lấy giá trị của Mức án
    const mucAnValue = parseInt(inputMucAn.value);
    
    // Tính số tiền
    if (!isNaN(mucAnValue)) {
        const soTien = mucAnValue * giaTien1Phut;
        inputTien.value = soTien.toLocaleString() + '$';
        
    } else {
        inputTien.value = '0$'; // Nếu mức án không hợp lệ, trả về 0
    }
}

// Hàm reset lại giá trị và trạng thái
function resetValue() {
    const toiDanh1 = document.getElementById('input-5');
    const toiDanh2 = document.getElementById('input-6');
    const toiDanh3 = document.getElementById('input-7');
    const selected = document.querySelectorAll('.selected');

    // Reset giá trị ô input về 0
    if (toiDanh1) toiDanh1.value = '';
    if (toiDanh2) toiDanh2.value = 0;
    if (toiDanh3) toiDanh3.value = 0;

    // Xóa trạng thái "selected" và reset background cho các phần tử được chọn
    selected.forEach(function (element) {
        element.classList.remove('selected'); // Xóa class "selected"
        element.style.background = ''; // Reset background color về mặc định
    });

    // Reset giá trị điểm (mucAn) và tiền (input-7)
    mucAn = 0;
    if (toiDanh2) toiDanh2.value = 0; // Đặt lại giá trị điểm trong input-6
    if (toiDanh3) toiDanh3.value = 0; // Đặt lại giá trị tiền trong input-7
}

// const crimes = [
//     { text: "Nhập cư trái phép", time: 30 },
//     { text: "Tàng trữ chất cấm trái phép (Cây thảo dược)", time: 30 },
//     { text: "Có lời lẽ xúc phạm đến danh dự, nhân phẩm người khác", time: 30 },
//     { text: "Sử dụng vũ khí thô sơ nơi công cộng", time: 30 },
//     { text: "Sử dụng vũ khí nóng nơi công cộng+Tàng trữ vũ khí nóng trái phép", time: 90, id: "not-qs" },
//     { text: "Sử dụng vũ khí nóng nơi công cộng (Có Giấy NVQS)", time: 30, id: "yet-qs" },
//     { text: "Tàng trữ vũ khí nóng", time: 30 },
//     { text: "Gây thương tích chưa nghiêm trọng cho người khác", time: 30 },
//     { text: "Chống đối người thi hành công vụ", time: 30 },
//     { text: "Xâm nhập trụ sở, nơi làm việc ban ngành nhà nước", time: 30 },
//     { text: "Xúc phạm Sĩ quan cảnh sát", time: 30 },
//     { text: "Sử dụng phương tiện gây thương tích chưa nghiêm trọng", time: 30 }
// ];

// // Hàm tạo bảng
// function generateTable() {
//     const tbody = document.querySelector(".box-law tbody");
//     tbody.innerHTML = ""; // Xóa nội dung cũ nếu có

//     crimes.forEach(crime => {
//         const tr = document.createElement("tr");
//         const td = document.createElement("td");

//         if (crime.id) td.id = crime.id; // Thêm ID nếu có
//         td.textContent = crime.text;
//         td.onclick = () => selectSuggestion(td, crime.time, 'input-5', 'input-6', 'input-7');

//         tr.appendChild(td);
//         tbody.appendChild(tr);
//     });
// }

// // Gọi hàm khi trang tải xong
// document.addEventListener("DOMContentLoaded", generateTable);
