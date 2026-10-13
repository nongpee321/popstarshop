import re
file_path = r"d:\pop-erp-food\ERPPOP-main\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

def inject_style(html, target_class, style_str):
    # This regex looks for class="..." containing the target_class
    # and injects the style_str into the tag if it doesn't already have it
    pattern = re.compile(rf'(<[^>]+class="[^"]*\b{target_class}\b[^"]*"[^>]*)>')
    
    def replacer(match):
        tag = match.group(1)
        if style_str in tag:
            return match.group(0) # Already has it
        
        # Check if it already has a style attribute
        if 'style="' in tag:
            return tag.replace('style="', f'style="{style_str} ', 1) + '>'
        else:
            return f'{tag} style="{style_str}">'
            
    return pattern.sub(replacer, html)

content = inject_style(content, "text-muted", "color: #64748b !important;")
content = inject_style(content, "text-danger", "color: #e11d48 !important;")
content = inject_style(content, "text-dark", "color: #0f172a !important;")
content = inject_style(content, "text-white", "color: #ffffff !important;")
content = inject_style(content, "bg-white", "background-color: #ffffff !important;")
content = inject_style(content, "bg-light", "background-color: #f8fafc !important;")
content = inject_style(content, "bg-danger", "background-color: #e11d48 !important;")
content = inject_style(content, "bg-danger-subtle", "background-color: #ffe4e6 !important;")
content = inject_style(content, "bg-success-subtle", "background-color: #d1fae5 !important;")
content = inject_style(content, "text-success", "color: #059669 !important;")
content = inject_style(content, "bg-warning-subtle", "background-color: #fef08a !important;")
content = inject_style(content, "text-warning", "color: #ca8a04 !important;")
content = inject_style(content, "bg-primary-subtle", "background-color: #e0f2fe !important;")
content = inject_style(content, "text-primary", "color: #0284c7 !important;")
content = inject_style(content, "db-header", "background-color: #ffffff !important;")
content = inject_style(content, "filter-bar", "background-color: #f1f5f9 !important;")
content = inject_style(content, "kpi-card", "background-color: #ffffff !important; color: #0f172a !important;")
content = inject_style(content, "chart-card", "background-color: #ffffff !important; color: #0f172a !important;")
content = inject_style(content, "table-card", "background-color: #ffffff !important; color: #0f172a !important;")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Injected comprehensive inline styles.")
