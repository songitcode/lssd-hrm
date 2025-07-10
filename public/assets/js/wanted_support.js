// Giới hạn nhập số 0–99
document.querySelectorAll('.gioi_han_number').forEach(function (input) {
    input.addEventListener('input', function () {
        if (this.value < 0 || this.value > 99) {
            setTimeout(() => {
                alert('Vui lòng không bấm số âm và lớn hơn 99');
            }, 750);
            this.value = 0;
        }
    });
});

// Xử lý input số lượng của từng tội danh
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

        if (td.classList.contains('bdtp')) {
            if (totalPhut > 1000) totalPhut = 1000;
            label = `Bạo Động Thành Phố Lần ${count}`;
        } else if (td.classList.contains('tcts')) {
            if (totalPhut > 1000) totalPhut = 1000;
            label = `Tấn công trụ sở Lần ${count}`;
        } else {
            label = count > 1 ? `${baseToiDanh}x${count}` : baseToiDanh;
        }

        input.value = count;

        selectedLaws.set(baseToiDanh, { phut: totalPhut, mucdo, label });
        td.classList.add('selected');
        updateToiDanhVaPhut();
    });
});

// Sao chép tội danh
document.getElementById('copyToiDanhBtn').addEventListener('click', function () {
    const toiDanhText = document.getElementById('show-toiDanh').textContent || '';
    if (!toiDanhText || toiDanhText.trim() === 'Không có') {
        showToast('Tội Danh Trống Rỗng');
        return;
    }

    const tempInput = document.createElement('textarea');
    tempInput.value = toiDanhText;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
    showToast('Đã Sao Chép');
});

// Hiện thông báo đẹp
function showToast(message) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 5000);
}

// Reset lại toàn bộ
function resetToiDanhCheck() {
    const btnResetToiDanh = document.getElementById('btnResetToiDanh');
    btnResetToiDanh.addEventListener('click', function () {
        document.querySelectorAll('.law-td').forEach(td => td.classList.remove('selected'));
        selectedLaws.clear();

        document.getElementById('show-toiDanh').textContent = 'Không có';
        document.getElementById('show-soPhut').textContent = '0p';

        updateToiDanhVaPhut();
    });
}
resetToiDanhCheck();

// Đồng bộ tên và CCCD
document.getElementById('input-proc-2').addEventListener('input', e => {
    document.getElementById('show-name').textContent = e.target.value || ' ';
});
document.getElementById('input-proc-3').addEventListener('input', e => {
    document.getElementById('show-cccd').textContent = e.target.value || 'Không xuất trình';
});

// ==== DATA ====
const showToiDanh = document.getElementById('show-toiDanh');
const showSoPhut = document.getElementById('show-soPhut');
const lawTds = document.querySelectorAll('.law-td');
const selectedLaws = new Map();

// ==== TÍNH TOÁN TỘI DANH & PHÚT ====
function updateToiDanhVaPhut() {
    let toiDanhList = [];
    let tongPhut = 0;
    let phutNhom_1 = 0;
    let phutBDTPNhom_2 = 0;
    let phutTCTSNhom_3 = 0;

    for (let [key, value] of selectedLaws) {
        toiDanhList.push(value.label || key);

        if (key.includes('Bạo Động Thành Phố')) {
            phutBDTPNhom_2 += value.phut;
        } else if (key.includes('Tấn công trụ sở')) {
            phutTCTSNhom_3 += value.phut;
        } else if (value.mucdo >= 1 && value.mucdo <= 5) {
            phutNhom_1 += value.phut;
        } else {
            tongPhut += value.phut;
        }
    }

    phutNhom_1 = Math.min(phutNhom_1, 500);
    phutBDTPNhom_2 = Math.min(phutBDTPNhom_2, 1000);
    phutTCTSNhom_3 = Math.min(phutTCTSNhom_3, 1000);

    tongPhut = phutNhom_1 + phutBDTPNhom_2 + phutTCTSNhom_3;

    showToiDanh.textContent = toiDanhList.join('+') || 'Không có';
    showSoPhut.textContent = `${tongPhut}p`;
}

// ==== CLICK CHỌN TỘI DANH ====
lawTds.forEach(td => {
    td.addEventListener('click', (event) => {
        if (event.target.tagName === 'INPUT') return;

        const toidan = td.dataset.toidan;
        const phut = parseInt(td.dataset.phut || '0');
        const mucdo = parseInt(td.dataset.mucdo || '1');

        const isSelected = td.classList.toggle('selected');
        if (isSelected) {
            const input = td.querySelector('.law-count-input');
            if (input) {
                input.dispatchEvent(new Event('input'));
            } else {
                selectedLaws.set(toidan, { phut, mucdo, label: toidan });
            }
        } else {
            selectedLaws.delete(toidan);
        }

        updateToiDanhVaPhut();
    });
});
