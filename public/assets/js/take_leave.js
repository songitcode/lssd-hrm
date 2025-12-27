function formatDate(dateString) {
    // Tạo một đối tượng Date từ chuỗi ngày tháng năm
    var date = new Date(dateString);

    // Lấy ngày, tháng và năm
    var day = ("0" + date.getDate()).slice(-2);  // Đảm bảo rằng ngày luôn có 2 chữ số
    var month = ("0" + (date.getMonth() + 1)).slice(-2);  // Tháng bắt đầu từ 0, nên cộng thêm 1
    var year = date.getFullYear();

    // Trả về ngày theo định dạng dd/mm/yyyy
    return day + "/" + month + "/" + year;
}

function copyAllToClipboard() {
    // Lấy giá trị từ các input và select
    var select1 = document.getElementById('select-1').value;
    var li1 = document.getElementById('quan-li-1').getAttribute('value');
    var li2 = document.getElementById('quan-li-2').getAttribute('value');
    var li3 = document.getElementById('quan-li-3').getAttribute('value');
    var select2 = document.getElementById('select-2').value;
    var select3 = document.getElementById('select-3').value;
    var input1 = document.getElementById('input-1').value;
    var select4 = document.getElementById('select-4').value;
    var input2 = document.getElementById('input-2').value;
    var select5 = document.getElementById('select-5').value;
    var input3 = document.getElementById('input-3').value;
    var input4 = document.getElementById('input-4').value;
    var input5 = document.getElementById('input-5').value;
    var input6 = document.getElementById('input-6').value;

    // Chuyển định dạng ngày tháng dd/mm/yyyy
    var formattedDate4 = new Date(input4).toLocaleDateString('en-GB');
    var formattedDate5 = new Date(input5).toLocaleDateString('en-GB');

    // Tạo nội dung sao chép
    var result = `
Kính Gửi Cục Trưởng
${select1}
Cục Trưởng: ${li1}
Phó Cục Trưởng: ${li2}
Trợ Lý Điều Hành: ${li3}
${select2 ? `Đội Trưởng: ${select2}` : ''.trim()}
${select3 ? `Đội Phó: ${select3}` : ''.trim()}
Tôi: ${input1}
Chức Vụ: ${select4}
Số Hiệu: ${input2}
Quân Hàm: ${select5}
Lý Do: ${input3}
Ngày Xin Nghỉ: ${formattedDate4}
Ngày Quay Lại: ${formattedDate5}
           Ký Tên: ${input6}`;

    // Loại bỏ các dòng trống và dồn các thông tin lại với nhau
    result = result.replace(/\n\s*\n/g, '\n').trim();  // Loại bỏ khoảng trắng thừa giữa các dòng

    // Tạo một thẻ textarea để sao chép
    var textarea = document.createElement('textarea');
    textarea.value = result;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);

    // Thông báo sao chép thành công
    alert('Đã sao chép thông tin!' + result);
}

function resetValue() {
    const inputs = document.querySelectorAll('input, select, textarea');
    alert("Ban co chac chan khong");
    inputs.forEach(input => {
        if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        } else {
            input.value = '';
        }
    });
}

function selectSuggestion(element, inputId) {
    const inputField = document.getElementById(inputId);

    // Thêm nội dung gợi ý vào ô input
    const newValue = element.textContent;
    if (inputField.value) {
        inputField.value += `, ${newValue}`; // Thêm dấu phẩy nếu input không rỗng
    } else {
        inputField.value = newValue;
    }

    // Xóa gợi ý được nhấn
    element.remove();
}

// Lấy ngày hiện tại
const today = new Date();
const yyyy = today.getFullYear();
const mm = String(today.getMonth() + 1).padStart(2, '0'); // Thêm 0 nếu tháng < 10
const dd = String(today.getDate()).padStart(2, '0'); // Thêm 0 nếu ngày < 10

// Định dạng ngày thành yyyy-MM-dd
const formattedDate = `${yyyy}-${mm}-${dd}`;

// Gán giá trị mặc định cho input
const dateInput = document.getElementById('input-4');
const dateInput_5 = document.getElementById('input-5');
dateInput.value = formattedDate;
dateInput_5.value = formattedDate;