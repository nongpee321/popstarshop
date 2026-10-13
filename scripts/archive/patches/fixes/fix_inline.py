import re
file_path = r"d:\pop-erp-food\ERPPOP-main\resources\views\dashboard.blade.php"
with open(file_path, "rb") as f:
    content = f.read()

# Strip BOM
if content.startswith(b'\xef\xbb\xbf'):
    content = content[3:]

content = content.decode('utf-8')

# Force inline colors
content = content.replace('class="text-danger', 'style="color: #e11d48 !important;" class="text-danger')
content = content.replace('class="text-muted', 'style="color: #64748b !important;" class="text-muted')
content = content.replace('class="text-dark', 'style="color: #0f172a !important;" class="text-dark')
content = content.replace('<h5 ', '<h5 style="color: #0f172a !important;" ')
content = content.replace('class="fw-bold fs-5"', 'style="color: #0f172a !important;" class="fw-bold fs-5"')
content = content.replace('class="fw-semibold fs-5"', 'style="color: #0f172a !important;" class="fw-semibold fs-5"')

# The alert banner text
content = content.replace('class="alert-banner', 'style="color: #9f1239 !important;" class="alert-banner')

# The countdown text
content = content.replace('id="dashboardRefreshCountdown"', 'id="dashboardRefreshCountdown" style="color: #64748b !important;"')

# Branch selector
content = content.replace('class="form-select', 'style="color: #0f172a !important; background-color: #f8fafc !important;" class="form-select')

# Filter bar text (ช่วงวันที่:, จาก, ถึง)
content = content.replace('class="input-group-text', 'style="color: #64748b !important; background-color: #ffffff !important;" class="input-group-text')
content = content.replace('class="form-control', 'style="color: #0f172a !important; background-color: #ffffff !important;" class="form-control')
content = content.replace('class="btn btn-sm btn-white', 'style="color: #64748b !important; background-color: #ffffff !important;" class="btn btn-sm btn-white')

# KPI Card text
content = content.replace('class="fs-3 fw-bold', 'style="color: #0f172a !important;" class="fs-3 fw-bold')

with open(file_path, "wb") as f:
    f.write(content.encode('utf-8'))
print("Applied inline styles and stripped BOM.")
