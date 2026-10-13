import re
file_path = r"d:\pop-erp-food\ERPPOP-main\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Add data-color-mode="night" to the CSS selector for dark mode
content = content.replace(
    'body.dark-mode, [data-bs-theme="dark"], [data-theme="dark"] {',
    'body.dark-mode, [data-bs-theme="dark"], [data-theme="dark"], html[data-color-mode="night"] {'
)

# And ensure there is a rule that overrides the layout's !important for dash-text and dash-muted inside dark mode
# Actually, the best way is to let my custom classes dictate their own color in night mode without being overridden.
# Since layout.blade.php uses !important on .text-dark and .text-muted, we can just remove .text-dark and .text-muted from our HTML!
content = content.replace("text-muted", "dash-muted")
content = content.replace("text-dark", "dash-text")
content = content.replace("text-danger", "dash-text-red")
content = content.replace("text-success", "dash-text-green")
content = content.replace("text-warning", "dash-text-yellow")
content = content.replace("text-primary", "dash-text-blue")
content = content.replace("bg-danger", "dash-bg-red")
content = content.replace("bg-white", "dash-bg-surface")
content = content.replace("bg-light", "dash-bg")

# Now add some CSS to cover the new classes if they don't exist
css_addition = """
    .dash-bg-red { background-color: var(--dash-red) !important; color: #ffffff !important; }
    .dash-bg-surface { background-color: var(--dash-surface) !important; }
    .dash-bg { background-color: var(--dash-bg) !important; }
    .dash-text-blue { color: var(--dash-blue) !important; }
"""

content = content.replace("</style>", css_addition + "</style>")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated CSS selectors to support data-color-mode=night")
