import os

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\bplus-ops\pos-workbench.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

old_strong = """@if($pythonPosInstaller)
                <strong>"""
new_strong = """@if($pythonPosInstaller)
                <strong>PopCentral Python POS: """

content = content.replace(old_strong, new_strong)

old_span = """<span>1?,-,>,T,1%1?,>,',",,T,, Web POS 1?,",,, ,?,,s,,,s,s,,,,? ,,T,, , ,,,-,,, ,,,, 1?,,,?,,,,,1?,S,1^,-,,?,,s ERP 1?,",,, ,?,,T,-,,?1?,,,1^,-, A ,,T,," {{ number_format($pythonPosInstaller['size_bytes'] / 1048576, 0) }} MB</span>"""
new_span = """<span>(Python + PySide6, Local SQLite) 1?,-,>,T,1%1?,>,',",,T,, Web POS...</span>"""

content = content.replace(",,,؅1,,1^,,1,,Y,1O", "Python + PySide6 / Local SQLite / ,,,؅1,,1^,,1,,Y,1O")

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Patched pos-workbench.blade.php")
