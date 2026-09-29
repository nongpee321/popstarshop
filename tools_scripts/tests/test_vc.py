def version_compare(v1, v2):
    return [int(x) for x in v1.split('.')] > [int(x) for x in v2.split('.')]
print(version_compare('1.10.57', '1.10.56'))
