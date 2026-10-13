import os

path = r'D:\pop-erp-food\ERPPOP-main\tests\Feature\PosCashierIdentityTest.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    "$this->assertTrue($response->getData(true)['success']);",
    "if (!isset($response->getData(true)['cashier'])) dd($response->getData(true));\n        $this->assertTrue($response->getData(true)['success']);"
)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
