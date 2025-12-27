document.querySelectorAll('.gioi_han_number').forEach(function (input) {
    input.addEventListener('input', function () {
        if (this.value < 0 || this.value > 99) {
            setTimeout(() => {
                alert('Vui lòng không bấm số âm và lớn hơn 99');
                // showToast('Vui lòng không bấm số âm và lớn hơn 99');
            }, 450);
            this.value = 0;
        }
    });
});
document.querySelectorAll('.law-count-input').forEach(input => {
    input.addEventListener('input', () => {
        const td = input.closest('.law-td');
        const baseToiDanh = td?.dataset.toidan?.trim();
        const basePhut = parseInt(td?.dataset.phut || '60');
        const rawMucDo = td?.dataset.mucdo;
        const mucdo = isNaN(rawMucDo) ? 3 : (rawMucDo === 'cong_don' ? 'cong_don' : parseInt(rawMucDo));

        let count = parseInt(input.value || '1');
        if (isNaN(count) || count < 1) count = 1;

        let label = '';
        let totalPhut = basePhut * count;

        // ===== XỬ LÝ TỘI MỨC ĐỘ 6 RIÊNG BIỆT =====
        if (td.classList.contains('bdtp')) {
            if (totalPhut > 1000) {
                totalPhut = 1000;
                // count = Math.floor(1000 / basePhut); // TỰ ĐỘNG GIỚI HẠN SỐ LẦN NHẬP
            }
            label = `Bạo Động Thành Phố Lần ${count}`;
        } else if (td.classList.contains('tcts')) {
            if (totalPhut > 1000) {
                totalPhut = 1000;
                // count = Math.floor(1000 / basePhut); // TỰ ĐỘNG GIỚI HẠN SỐ LẦN NHẬP THEO SỐ PHÚT
            }
            label = `Tấn công trụ sở Lần ${count}`;
        } else {
            label = count > 1 ? `${baseToiDanh}x${count}` : baseToiDanh;
        }

        input.value = count; // Đặt lại giá trị input nếu bị giới hạn

        // ===== LƯU VÀO MAP =====
        selectedLaws.set(baseToiDanh, {
            phut: totalPhut,
            mucdo: mucdo,
            label: label
        });

        td.classList.add('selected');
        updateToiDanhVaPhut();
    });
});
document.getElementById('copyToiDanhBtn').addEventListener('click', function () {
    const toiDanhText = document.getElementById('show-toiDanh').textContent || '';

    if (!toiDanhText || toiDanhText.trim() === 'Không có') {
        // alert('Chưa có tội danh để sao chép!');
        showToast('Tội Danh Trống Rỗng');
        return;
    }

    // Tạo thẻ tạm để copy
    const tempInput = document.createElement('textarea');
    tempInput.value = toiDanhText;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    // Thông báo
    // alert('Đã sao chép tội danh!' + toiDanhText.textContent);
    // Hiện thông báo đẹp
    showToast('Đã Sao Chép');
});
function showToast(message) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 5000);
}

///// RESET TỘI DANH VÀ CHECK BOX
function resetToiDanhCheck() {
    const btnResetToiDanh = document.getElementById('btnResetToiDanh');
    btnResetToiDanh.addEventListener('click', function () {
        // Xóa tất cả tội danh đã chọn
        const lawTds = document.querySelectorAll('.law-td');
        lawTds.forEach(td => {
            td.classList.remove('selected');
        });

        // Xóa checkbox
        const checkboxes = document.querySelectorAll('.input_proc[type="checkbox"]');
        checkboxes.forEach(cb => {
            cb.checked = false;
        });

        // Reset Map chứa các tội danh đã chọn
        selectedLaws.clear();

        // Reset trạng thái tội danh đặc biệt
        isSpecialLawSelected = false;

        // Reset giao diện
        const showToiDanh = document.getElementById('show-toiDanh');
        const showSoPhut = document.getElementById('show-soPhut');
        showToiDanh.textContent = 'Không có';
        showSoPhut.textContent = '0p';

        // Cập nhật lại dữ liệu giao diện (nếu cần logic thêm)
        updateToiDanhVaPhut();
    });
}
resetToiDanhCheck();

// Đồng bộ tên và CCCD từ input đến hiển thị
const inputName = document.getElementById('input-proc-2');
const inputCCCD = document.getElementById('input-proc-3');
const showName = document.getElementById('show-name');
const showCCCD = document.getElementById('show-cccd');

// Gắn sự kiện khi người dùng nhập vào input
inputName.addEventListener('input', () => {
    showName.textContent = inputName.value || ' ';
});

inputCCCD.addEventListener('input', () => {
    showCCCD.textContent = inputCCCD.value || 'Không xuất trình';
});

//// CHECK TOI DANH VÀ PHÚT 
// ==== DOM Elements ====
const showToiDanh = document.getElementById('show-toiDanh');
const showSoPhut = document.getElementById('show-soPhut');
const lawTds = document.querySelectorAll('.law-td');
const checkboxGiam = document.querySelectorAll('.input_proc[type="checkbox"][data-type="giam"]');
const checkboxCustom = document.querySelectorAll('.input_proc[type="checkbox"]:not([data-type="giam"])');
const cbNVQS = document.getElementById('input-proc-4');
const cbTangTru = document.getElementById('input-proc-5');
const cbLuatTieuDung = document.getElementById('viPhamLuatTieuDungCheck');
const inputSoBill = document.getElementById('input-proc-1');
const tdDacBiet = document.getElementById('td-dac-biet-1');

// ==== Data Storage ====
const selectedLaws = new Map(); // key = toidan, value = { phut, mucdo }
let isSpecialLawSelected = false;

// ==== Update ToiDanh & Phut ====
function updateToiDanhVaPhut(event = null) {
    // Xóa các key tạm tự động thêm từ checkbox NVQS, Tàng Trữ
    [selectedLaws.delete(cbNVQS.dataset.toidan), selectedLaws.delete(cbTangTru.dataset.toidan)];

    // NVQS vs Tàng Trữ xung đột
    if (cbNVQS.checked && cbTangTru.checked) {
        if (event?.target === cbNVQS) cbTangTru.checked = false;
        else cbNVQS.checked = false;
    }

    // Tạm xóa tội đặc biệt nếu check NVQS hoặc Tàng Trữ
    if ((cbNVQS.checked || cbTangTru.checked) && selectedLaws.has(tdDacBiet.dataset.toidan)) {
        selectedLaws.delete(tdDacBiet.dataset.toidan);
        tdDacBiet.classList.remove('selected');
        isSpecialLawSelected = true; // đánh dấu là hệ thống tự xóa
    }

    // Khôi phục tội đặc biệt nếu cần (nếu hệ thống từng tự xóa)
    if (!cbNVQS.checked && !cbTangTru.checked && isSpecialLawSelected && !selectedLaws.has(tdDacBiet.dataset.toidan)) {
        selectedLaws.set(tdDacBiet.dataset.toidan, {
            phut: parseInt(tdDacBiet.dataset.phut || '0'),
            mucdo: parseInt(tdDacBiet.dataset.mucdo || '1')
        });
        tdDacBiet.classList.add('selected');
        isSpecialLawSelected = false;
    }

    let toiDanhList = [];
    let giamList = [];
    let tongPhut = 0;
    let phutNhom_1 = 0;
    let phutBDTPNhom_2 = 0;
    let phutTCTSNhom_3 = 0;

    for (let [key, value] of selectedLaws) {
        toiDanhList.push(value.label || key);

        // Phân loại giới hạn theo tội danh hoặc mức độ
        if (key.includes('Bạo Động Thành Phố')) {
            phutBDTPNhom_2 += value.phut;
        } else if (key.includes('Tấn công trụ sở')) {
            phutTCTSNhom_3 += value.phut;
        } else if (value.mucdo >= 1 && value.mucdo <= 5) {
            phutNhom_1 += value.phut;
        } else {
            // Nếu không thuộc nhóm nào thì vẫn cộng
            tongPhut += value.phut;
        }
    }

    // Giới hạn nhóm
    phutNhom_1 = Math.min(phutNhom_1, 500);
    phutBDTPNhom_2 = Math.min(phutBDTPNhom_2, 1000);
    phutTCTSNhom_3 = Math.min(phutTCTSNhom_3, 1000);

    // Tổng tất cả nhóm giới hạn
    tongPhut = phutNhom_1 + phutBDTPNhom_2 + phutTCTSNhom_3;

    // Add luật từ checkbox thường
    checkboxCustom.forEach(cb => {
        if (cb.checked && cb !== cbNVQS && cb !== cbTangTru && cb !== cbLuatTieuDung) {
            const td = cb.dataset.toidan;
            const phut = parseInt(cb.dataset.phut || '0');
            if (td) toiDanhList.push(td);
            tongPhut += phut;
        }
    });

    ///// XỬ LÝ 2 CHECKBOX + BILL
    if (cbNVQS.checked) {
        const td = cbNVQS.dataset.toidan;
        const mucdo = parseInt(cbNVQS.dataset.mucdo || '1');
        const phut = parseInt(cbNVQS.dataset.phut || '0');

        // Tính phần còn lại cho nhóm 1
        const phutConLai = Math.max(0, 500 - phutNhom_1);
        const phutThucTe = Math.min(phut, phutConLai);

        phutNhom_1 += phutThucTe;
        tongPhut += phut;
        toiDanhList.push(td);
        console.log(phutNhom_1);
        //Cho vào selectedLaws để mucdo hoạt động
        selectedLaws.set(td, {
            phut: phutThucTe,
            mucdo,
            label: td
        });
    } else if (cbTangTru.checked) {
        const td = cbTangTru.dataset.toidan;
        const mucdo = parseInt(cbTangTru.dataset.mucdo || '1');
        const phut = parseInt(cbTangTru.dataset.phut || '0');

        const phutConLai = Math.max(0, 500 - phutNhom_1);
        const phutThucTe = Math.min(phut, phutConLai);

        phutNhom_1 += phutThucTe;
        toiDanhList.push(td);
        tongPhut += phut;

        selectedLaws.set(td, {
            phut: phutThucTe,
            mucdo,
            label: td
        });
    }

    // Vi phạm luật tiêu dùng (chỉ nếu có Mức độ 2+)
    if (cbLuatTieuDung.checked) {
        const hasMucDo2 = [...selectedLaws.values()].some(item => item.mucdo >= 2);
        if (hasMucDo2) {
            const bill = parseInt(inputSoBill.value || '0');
            const phut = bill * 5;

            // Nếu nhóm 1 đã đạt 500p thì không cộng thêm
            if (phutNhom_1 < 500) {
                const phutConLai = Math.max(0, 500 - phutNhom_1);
                const phutThucTe = Math.min(phutConLai, phut);
                phutNhom_1 += phutThucTe; // cộng vào nhóm 1 thay vì trực tiếp vào tổng
                // Cộng vào tongPhut vì phút nhóm 1 đã giới hạn riêng trước đó
                tongPhut += phutThucTe;
            }

            // Vẫn hiển thị tên tội danh dù không cộng phút
            toiDanhList.push(`Vi phạm luật người tiêu dùng (${bill}bill)`);
        } else {
            cbLuatTieuDung.checked = false;
        }
    }


    // Giảm án
    checkboxGiam.forEach(cb => {
        if (cb.checked) {
            const td = cb.dataset.toidan;
            const phut = parseInt(cb.dataset.phut || '0');
            if (td) giamList.push(td);
            tongPhut += phut;
        }
    });

    let giamStr = '';
    if (giamList.length === 1) {
        giamStr = `(Giảm 10p ${giamList[0]})`;
    } else if (giamList.length === 2) {
        giamStr = `(Giảm 20p ${giamList.join('+')})`;
    }

    const fullToiDanh = toiDanhList.join('+') + (giamStr ? ` ${giamStr}` : '');
    showToiDanh.textContent = fullToiDanh || 'Không có';
    showSoPhut.textContent = `${tongPhut}p`;

}

// ==== Event Binding ====
lawTds.forEach(td => {
    td.addEventListener('click', (event) => {
        // Nếu đang click vào input thì bỏ qua
        if (event.target.tagName === 'INPUT') return;

        const toidan = td.dataset.toidan;
        const phut = parseInt(td.dataset.phut || '0');
        const mucdo = parseInt(td.dataset.mucdo || '1');

        const isSelected = td.classList.toggle('selected');

        if (isSelected) {
            //  Nếu đang có check NVQS hoặc Tàng Trữ và user muốn check lại tội đặc biệt ➜ phải bỏ 2 cái kia
            if (toidan === tdDacBiet.dataset.toidan) {
                if (cbNVQS.checked) cbNVQS.checked = false;
                if (cbTangTru.checked) cbTangTru.checked = false;
                isSpecialLawSelected = false;
            }

            selectedLaws.set(toidan, { phut, mucdo });
        } else {
            selectedLaws.delete(toidan);
        }

        if (td.id === 'td-dac-biet-1') {
            isSpecialLawSelected = false;
        }
        const hasMucDo2 = [...selectedLaws.values()].some(item => item.mucdo >= 2);
        if (!hasMucDo2 && cbLuatTieuDung.checked) {
            cbLuatTieuDung.checked = false;
        }

        if (isSelected) {
            const input = td.querySelector('.law-count-input');
            if (input) {
                input.dispatchEvent(new Event('input')); // 🔁 GỌI lại sự kiện input để xử lý luôn
            } else {
                selectedLaws.set(toidan, { phut, mucdo });
            }
        } else {
            selectedLaws.delete(toidan);
        }

        updateToiDanhVaPhut();
    });
});

[...checkboxGiam, ...checkboxCustom].forEach(cb => {
    cb.addEventListener('change', updateToiDanhVaPhut);
});

cbLuatTieuDung.addEventListener('change', updateToiDanhVaPhut);
inputSoBill.addEventListener('input', () => {
    if (cbLuatTieuDung.checked) updateToiDanhVaPhut();
});

const customReduceInput = document.getElementById('input-proc-8'); // input số phút

customReduceInput.addEventListener('input', () => {
    let reduceValue = parseInt(customReduceInput.value) || 0;
    if (reduceValue < 0) reduceValue = 0;

    // Lấy tổng phút gốc từ selectedLaws
    let tongPhut = 0;
    for (let [key, value] of selectedLaws) {
        tongPhut += value.phut;
    }

    // Áp dụng luật nhóm 1, 2, 3 + checkbox khác nếu cần
    // Hoặc dùng updateToiDanhVaPhut() để tính lại tổng, rồi trừ giảm
    updateToiDanhVaPhut();
    let currentPhut = parseInt(showSoPhut.textContent) || 0;

    // Trừ số phút từ input
    let phutSauGiam = Math.max(0, currentPhut - reduceValue);
    showSoPhut.textContent = `${phutSauGiam}p`;

    // Cập nhật show-toiDanh
    let currentToiDanh = showToiDanh.textContent.replace(/\s*\(-\d+p\)/g, '');
    if (reduceValue > 0) {
        showToiDanh.textContent = `${currentToiDanh} (-${reduceValue}p)`;
    } else {
        showToiDanh.textContent = currentToiDanh || 'Không có';
    }
});

