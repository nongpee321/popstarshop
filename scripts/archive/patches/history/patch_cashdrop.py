import os
import re

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\pos\index.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# I will replace the submitCloseShift with recordCashDrop + submitCloseShift
new_method = """        async recordCashDrop() {
            if (!this.activeShift) return;
            const { value: formValues } = await Swal.fire({
                title: 'นำเงินนำส่ง (Cash Drop)',
                html:
                    '<input id="swal-drop-amount" class="swal2-input" type="number" placeholder="จำนวนเงิน" min="1" step="0.25">' +
                    '<input id="swal-drop-reason" class="swal2-input" type="text" placeholder="หมายเหตุ / ผู้นำส่ง (ถ้ามี)">',
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'บันทึกนำส่ง',
                cancelButtonText: 'ยกเลิก',
                preConfirm: () => {
                    const amount = document.getElementById('swal-drop-amount').value;
                    const reason = document.getElementById('swal-drop-reason').value;
                    if (!amount || amount <= 0) {
                        Swal.showValidationMessage('กรุณาระบุจำนวนเงินให้ถูกต้อง');
                        return false;
                    }
                    return { amount: amount, reason: reason };
                }
            });

            if (formValues) {
                this.shiftProcessing = true;
                try {
                    const res = await fetch('/pos/shift/cash-movement', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({
                            branch_id: this.branchId,
                            cashier_id: this.cashierId,
                            shift_id: this.activeShift.id,
                            movement_type: 'drop',
                            amount: Number(formValues.amount),
                            reason: formValues.reason
                        })
                    });
                    const data = await res.json();
                    if (!res.ok || !data.success) {
                        erpPopup('error', data.message || 'บันทึกนำเงินส่งไม่สำเร็จ');
                        return;
                    }
                    erpToast('success', 'บันทึกนำเงินส่งเรียบร้อยแล้ว');
                    this.loadActiveShift();
                    this.closeShiftModalOpen = false;
                } catch (e) {
                    erpPopup('error', 'เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ');
                } finally {
                    this.shiftProcessing = false;
                }
            }
        },

        async submitCloseShift() {"""

content = content.replace("        async submitCloseShift() {", new_method)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Injected recordCashDrop")
