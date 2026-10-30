# DESIGN.md — FoodFlow UI Design System

## 1. Purpose and Scope

This file defines the visual and interaction guidelines for the existing FoodFlow Laravel application, with immediate focus on improving the **Master Data Bahan Baku** page.

Use the supplied FoodFlow production-dashboard screenshot as the primary visual reference. The current dashboard's restrained, operational style should carry through to the Bahan Baku page so both screens feel like parts of the same application.

This is a UI improvement for an existing application, not a request to rebuild the product or replace its backend.

## 2. Visual Direction

The interface should feel like a practical, trustworthy operational system used by staff every day.

- Light, cool off-white page background.
- White content surfaces.
- Dark navy/charcoal primary text.
- Muted slate secondary text.
- Blue for primary actions and links.
- Thin, pale borders to separate sections.
- Small, restrained corner radii.
- Compact but comfortable spacing.
- Clear table headers and row separators.
- Small, meaningful status pills.
- Simple line icons with consistent stroke weight.

Keep the visual language close to the supplied screenshot: quiet, orderly, readable, and data-focused. Do not turn the page into a marketing landing page or a flashy generic SaaS dashboard.

## 3. Design Tokens

Treat these as starting values. Reuse existing project tokens when equivalent ones already exist.

- Page background: `#F8FAFC`
- Surface: `#FFFFFF`
- Primary text: `#1F2937`
- Secondary text: `#64748B`
- Muted text: `#94A3B8`
- Border: `#E2E8F0`
- Subtle table/header background: `#F8FAFC`
- Primary blue: `#3156A6`
- Primary blue hover: `#244786`
- Informational background: `#E8F1FF`
- Informational text: `#3156A6`
- Success background: `#E7F8F0`
- Success text: `#16805D`
- Warning background: `#FFF3CD`
- Warning text: `#A96518`
- Critical background: `#FDE8E8`
- Critical text: `#B42332`

Status colors must have consistent meaning. Always show a readable status label; never rely on color alone. Avoid using all accent colors at once.

## 4. Typography

- Use the existing application font unless there is a strong reason to change it.
- Use compact, readable sans-serif typography.
- Page titles should be clear but not oversized.
- Section titles should use a modest semibold weight.
- Supporting descriptions and metadata should be smaller and muted, but still readable.
- Table headers should be compact, uppercase only if consistent with the current dashboard, and visually quieter than table values.
- Align quantities and other numeric values consistently.
- Use tabular numerals for stock quantities when supported by the current font.
- Avoid excessive bold text, letter spacing, and arbitrary font-size changes.

## 5. Page Layout and Shared Shell

Preserve the existing FoodFlow application shell, including its current sidebar, header, content width, navigation, and responsive behavior.

- Keep the page content aligned with other FoodFlow operational pages.
- Use a clear page heading with a short, useful description where appropriate.
- Place the primary action near the page heading, if that action already exists and is permitted.
- Use consistent spacing between page heading, filters, alerts, and the main table.
- Use white panels with thin borders rather than heavy shadows.
- Avoid unnecessary nested cards and excessive empty space.
- Keep breadcrumbs, account details, dates, and navigation items only when already supported by the application.

Do not redesign unrelated pages or change the shared shell unless required for consistency or responsive behavior.

## 6. Master Data Bahan Baku Page

### Page heading

Use a clear heading such as “Bahan Baku” or the existing page title used by the application. Add a short description only if it helps users understand the page's purpose.

Display the existing primary action (for example, adding a new ingredient) prominently but calmly. Do not add a button unless the route, permissions, and action already exist.

### Summary information

Only display summary metrics if they are useful to staff and can be derived reliably from backend data.

Possible metrics, only when supported:
- Total active ingredients.
- Ingredients below minimum stock.
- Ingredients with depleted stock.

Do not add a row of summary cards just to fill space. If the page is primarily a master-data list and these metrics do not help the workflow, prioritize the filters and table instead. Never invent figures, trends, or percentages.

### Search and filters

Place search and existing filters together in a compact, clearly aligned toolbar above the table.

Use only controls backed by current application functionality, such as:
- Search by ingredient name or code.
- Category filter.
- Active/inactive status filter.
- Stock status filter, if the backend supports it.

Keep labels and placeholders clear. Preserve existing query parameters, validation, and filter behavior. Do not render decorative controls that do not work.

### Ingredient table

The table is the primary focus of the page. Match the production dashboard's restrained table style.

- Use a pale neutral header row and thin horizontal separators.
- Keep column labels readable and visually understated.
- Use consistent row heights and alignment.
- Give ingredient names the strongest emphasis within each row.
- Keep secondary identifiers or category information muted and smaller when those fields exist.
- Right-align numeric quantities where appropriate.
- Always show the unit for stock quantities.
- Keep status labels compact and consistent.
- Align row actions consistently at the end of each row.
- Avoid unnecessary icons in every cell.
- Use existing pagination and sorting behavior when available.

The actual columns must be based on existing models, migrations, controller data, and business rules. Candidate fields may include ingredient name, code, category, unit, minimum stock, current stock, status, and actions—but do not assume all of these exist.

On mobile, preserve table readability using the project's existing responsive pattern, horizontal scrolling, or another accessible alternative. Do not cause the entire page to overflow horizontally.

### Stock status

Use the existing backend's stock-status rules. Do not create new thresholds in the view.

When supported, use restrained labels such as “Aman”, “Menipis”, or “Habis”. The displayed label and color must agree with the actual backend calculation. If the application uses different terminology, retain the application's terminology.

## 7. Components and Interaction

### Buttons and links
- Primary actions use the existing blue accent.
- Secondary actions use neutral styling.
- Destructive actions must be distinguishable and confirmed when appropriate.
- Links should be visually identifiable without being overly decorative.
- Disabled, loading, success, and error states should be clear where relevant.

### Forms
- Keep labels visible.
- Preserve existing server-side validation and error messages.
- Make validation errors understandable and associated with the relevant field.
- Preserve existing form submission behavior and permissions.

### Status indicators
- Use compact, low-saturation status pills similar to those in the production dashboard.
- Use consistent labels and colors across the application.
- Do not use a badge when plain text would communicate the information more clearly.

### Icons
- Reuse the icon library already present in the project.
- Keep icons simple and consistent.
- Use icons only when they improve recognition or scanning.
- Do not add a new icon package without a clear need.

## 8. Anti-Slop Rules

Avoid:
- Large gradients or colorful decorative backgrounds.
- Excessive rounded cards, pill-shaped controls, and drop shadows.
- Four or more summary cards added only for visual effect.
- Decorative charts unrelated to the page's task.
- Random icons, emoji, and illustration.
- Oversized headings and excessive bold text.
- Repeated badges for ordinary text.
- Fake notifications, live indicators, activity, or user data.
- Mock ingredient rows or fabricated stock figures in production UI.
- Buttons, filters, and links that do not perform a real action.
- Unnecessary animation or hover effects.
- Replacing a useful table with cards on desktop without a clear usability reason.

Before adding a UI element, verify that it supports a real task, uses available data, and fits the established FoodFlow visual system.

## 9. Laravel and Backend Compatibility

Inspect the existing code before editing.

Review the relevant:
- `routes/web.php` and route names.
- Controller and data passed to the view.
- Eloquent models and relationships.
- Database migrations and actual columns.
- Form requests and validation rules.
- Authentication, roles, and authorization policies.
- Blade templates, layouts, and reusable components.
- Existing CSS framework, JavaScript, and frontend dependencies.

Implementation requirements:
- Treat the existing backend as the source of truth.
- Preserve routes, controller behavior, permissions, validation, and business logic.
- Bind visible values to real backend data.
- Reuse current queries, relationships, sorting, filters, and pagination where possible.
- Do not assume a field or relationship exists before inspecting the code.
- Do not change database schema or business logic solely to match a screenshot.
- Do not add production-facing mock data.
- Do not introduce dependencies unless necessary and justified.
- Keep presentation changes separate from business logic where practical.
- Do not change unrelated modules.

If a visual element in the reference is not supported by the current backend, omit it rather than faking it. Report any genuine backend gap separately instead of silently implementing a workaround.

## 10. Responsive Behavior

- Desktop: preserve the shared navigation and use the available content width for the table.
- Tablet: reduce excessive spacing and keep filters usable.
- Mobile: adapt navigation to the existing responsive pattern, stack toolbar controls where necessary, and keep primary actions accessible.
- Prevent page-level horizontal overflow.
- Maintain readable text, visible focus states, and accessible controls at all sizes.

## 11. Implementation Workflow and Verification

1. Inspect the existing Bahan Baku page and its backend data flow.
2. Identify which elements are already implemented and which are missing.
3. Reuse the current FoodFlow layout and component patterns.
4. Update the page styling and layout incrementally.
5. Preserve current behavior while improving hierarchy, spacing, table readability, and status presentation.
6. Test the page's existing routes, create/edit flows, search, filters, pagination, and authorization where applicable.
7. Check desktop and mobile layouts in a browser.
8. Run relevant existing tests and report any failures honestly.

Do not claim that a feature or visual check passed unless it was actually tested.

## 12. Definition of Done

The Bahan Baku page is ready when:
- It visually belongs to the same FoodFlow application as the production dashboard.
- The page is readable, compact, and operationally focused.
- All displayed data comes from the existing backend.
- Existing actions, permissions, and validation still work.
- No fake controls, fabricated data, or unnecessary decorative components remain.
- The page works at desktop and mobile widths.
- Relevant tests and browser checks have been performed and their results reported.
# DESIGN.md — FoodFlow UI Design System

## 1. Purpose and Scope

This file defines the visual and interaction guidelines for the existing FoodFlow Laravel application, with immediate focus on improving the **Master Data Bahan Baku** page.

Use the supplied FoodFlow production-dashboard screenshot as the primary visual reference. The current dashboard's restrained, operational style should carry through to the Bahan Baku page so both screens feel like parts of the same application.

This is a UI improvement for an existing application, not a request to rebuild the product or replace its backend.

## 2. Visual Direction

The interface should feel like a practical, trustworthy operational system used by staff every day.

- Light, cool off-white page background.
- White content surfaces.
- Dark navy/charcoal primary text.
- Muted slate secondary text.
- Blue for primary actions and links.
- Thin, pale borders to separate sections.
- Small, restrained corner radii.
- Compact but comfortable spacing.
- Clear table headers and row separators.
- Small, meaningful status pills.
- Simple line icons with consistent stroke weight.

Keep the visual language close to the supplied screenshot: quiet, orderly, readable, and data-focused. Do not turn the page into a marketing landing page or a flashy generic SaaS dashboard.

## 3. Design Tokens

Treat these as starting values. Reuse existing project tokens when equivalent ones already exist.

- Page background: `#F8FAFC`
- Surface: `#FFFFFF`
- Primary text: `#1F2937`
- Secondary text: `#64748B`
- Muted text: `#94A3B8`
- Border: `#E2E8F0`
- Subtle table/header background: `#F8FAFC`
- Primary blue: `#3156A6`
- Primary blue hover: `#244786`
- Informational background: `#E8F1FF`
- Informational text: `#3156A6`
- Success background: `#E7F8F0`
- Success text: `#16805D`
- Warning background: `#FFF3CD`
- Warning text: `#A96518`
- Critical background: `#FDE8E8`
- Critical text: `#B42332`

Status colors must have consistent meaning. Always show a readable status label; never rely on color alone. Avoid using all accent colors at once.

## 4. Typography

- Use the existing application font unless there is a strong reason to change it.
- Use compact, readable sans-serif typography.
- Page titles should be clear but not oversized.
- Section titles should use a modest semibold weight.
- Supporting descriptions and metadata should be smaller and muted, but still readable.
- Table headers should be compact, uppercase only if consistent with the current dashboard, and visually quieter than table values.
- Align quantities and other numeric values consistently.
- Use tabular numerals for stock quantities when supported by the current font.
- Avoid excessive bold text, letter spacing, and arbitrary font-size changes.

## 5. Page Layout and Shared Shell

Preserve the existing FoodFlow application shell, including its current sidebar, header, content width, navigation, and responsive behavior.

- Keep the page content aligned with other FoodFlow operational pages.
- Use a clear page heading with a short, useful description where appropriate.
- Place the primary action near the page heading, if that action already exists and is permitted.
- Use consistent spacing between page heading, filters, alerts, and the main table.
- Use white panels with thin borders rather than heavy shadows.
- Avoid unnecessary nested cards and excessive empty space.
- Keep breadcrumbs, account details, dates, and navigation items only when already supported by the application.

Do not redesign unrelated pages or change the shared shell unless required for consistency or responsive behavior.

## 6. Master Data Bahan Baku Page

### Page heading

Use a clear heading such as “Bahan Baku” or the existing page title used by the application. Add a short description only if it helps users understand the page's purpose.

Display the existing primary action (for example, adding a new ingredient) prominently but calmly. Do not add a button unless the route, permissions, and action already exist.

### Summary information

Only display summary metrics if they are useful to staff and can be derived reliably from backend data.

Possible metrics, only when supported:
- Total active ingredients.
- Ingredients below minimum stock.
- Ingredients with depleted stock.

Do not add a row of summary cards just to fill space. If the page is primarily a master-data list and these metrics do not help the workflow, prioritize the filters and table instead. Never invent figures, trends, or percentages.

### Search and filters

Place search and existing filters together in a compact, clearly aligned toolbar above the table.

Use only controls backed by current application functionality, such as:
- Search by ingredient name or code.
- Category filter.
- Active/inactive status filter.
- Stock status filter, if the backend supports it.

Keep labels and placeholders clear. Preserve existing query parameters, validation, and filter behavior. Do not render decorative controls that do not work.

### Ingredient table

The table is the primary focus of the page. Match the production dashboard's restrained table style.

- Use a pale neutral header row and thin horizontal separators.
- Keep column labels readable and visually understated.
- Use consistent row heights and alignment.
- Give ingredient names the strongest emphasis within each row.
- Keep secondary identifiers or category information muted and smaller when those fields exist.
- Right-align numeric quantities where appropriate.
- Always show the unit for stock quantities.
- Keep status labels compact and consistent.
- Align row actions consistently at the end of each row.
- Avoid unnecessary icons in every cell.
- Use existing pagination and sorting behavior when available.

The actual columns must be based on existing models, migrations, controller data, and business rules. Candidate fields may include ingredient name, code, category, unit, minimum stock, current stock, status, and actions—but do not assume all of these exist.

On mobile, preserve table readability using the project's existing responsive pattern, horizontal scrolling, or another accessible alternative. Do not cause the entire page to overflow horizontally.

### Stock status

Use the existing backend's stock-status rules. Do not create new thresholds in the view.

When supported, use restrained labels such as “Aman”, “Menipis”, or “Habis”. The displayed label and color must agree with the actual backend calculation. If the application uses different terminology, retain the application's terminology.

## 7. Components and Interaction

### Buttons and links
- Primary actions use the existing blue accent.
- Secondary actions use neutral styling.
- Destructive actions must be distinguishable and confirmed when appropriate.
- Links should be visually identifiable without being overly decorative.
- Disabled, loading, success, and error states should be clear where relevant.

### Forms
- Keep labels visible.
- Preserve existing server-side validation and error messages.
- Make validation errors understandable and associated with the relevant field.
- Preserve existing form submission behavior and permissions.

### Status indicators
- Use compact, low-saturation status pills similar to those in the production dashboard.
- Use consistent labels and colors across the application.
- Do not use a badge when plain text would communicate the information more clearly.

### Icons
- Reuse the icon library already present in the project.
- Keep icons simple and consistent.
- Use icons only when they improve recognition or scanning.
- Do not add a new icon package without a clear need.

## 8. Anti-Slop Rules

Avoid:
- Large gradients or colorful decorative backgrounds.
- Excessive rounded cards, pill-shaped controls, and drop shadows.
- Four or more summary cards added only for visual effect.
- Decorative charts unrelated to the page's task.
- Random icons, emoji, and illustration.
- Oversized headings and excessive bold text.
- Repeated badges for ordinary text.
- Fake notifications, live indicators, activity, or user data.
- Mock ingredient rows or fabricated stock figures in production UI.
- Buttons, filters, and links that do not perform a real action.
- Unnecessary animation or hover effects.
- Replacing a useful table with cards on desktop without a clear usability reason.

Before adding a UI element, verify that it supports a real task, uses available data, and fits the established FoodFlow visual system.

## 9. Laravel and Backend Compatibility

Inspect the existing code before editing.

Review the relevant:
- `routes/web.php` and route names.
- Controller and data passed to the view.
- Eloquent models and relationships.
- Database migrations and actual columns.
- Form requests and validation rules.
- Authentication, roles, and authorization policies.
- Blade templates, layouts, and reusable components.
- Existing CSS framework, JavaScript, and frontend dependencies.

Implementation requirements:
- Treat the existing backend as the source of truth.
- Preserve routes, controller behavior, permissions, validation, and business logic.
- Bind visible values to real backend data.
- Reuse current queries, relationships, sorting, filters, and pagination where possible.
- Do not assume a field or relationship exists before inspecting the code.
- Do not change database schema or business logic solely to match a screenshot.
- Do not add production-facing mock data.
- Do not introduce dependencies unless necessary and justified.
- Keep presentation changes separate from business logic where practical.
- Do not change unrelated modules.

If a visual element in the reference is not supported by the current backend, omit it rather than faking it. Report any genuine backend gap separately instead of silently implementing a workaround.

## 10. Responsive Behavior

- Desktop: preserve the shared navigation and use the available content width for the table.
- Tablet: reduce excessive spacing and keep filters usable.
- Mobile: adapt navigation to the existing responsive pattern, stack toolbar controls where necessary, and keep primary actions accessible.
- Prevent page-level horizontal overflow.
- Maintain readable text, visible focus states, and accessible controls at all sizes.

## 11. Implementation Workflow and Verification

1. Inspect the existing Bahan Baku page and its backend data flow.
2. Identify which elements are already implemented and which are missing.
3. Reuse the current FoodFlow layout and component patterns.
4. Update the page styling and layout incrementally.
5. Preserve current behavior while improving hierarchy, spacing, table readability, and status presentation.
6. Test the page's existing routes, create/edit flows, search, filters, pagination, and authorization where applicable.
7. Check desktop and mobile layouts in a browser.
8. Run relevant existing tests and report any failures honestly.

Do not claim that a feature or visual check passed unless it was actually tested.

## 12. Definition of Done

The Bahan Baku page is ready when:
- It visually belongs to the same FoodFlow application as the production dashboard.
- The page is readable, compact, and operationally focused.
- All displayed data comes from the existing backend.
- Existing actions, permissions, and validation still work.
- No fake controls, fabricated data, or unnecessary decorative components remain.
- The page works at desktop and mobile widths.
- Relevant tests and browser checks have been performed and their results reported.

---

## 13. Referensi Token Desain
- **Primary Blue**: `theme('colors.blue.600')` ← didefinisikan di `tailwind.config.js`.
- **Background Page**: `var(--color-bg-page)` ← didefinisikan di `resources/css/tokens.css`.
- **Surface**: `#FFFFFF` (langsung karena putih standar).
- **Primary Text**: `#1F2937` ← `text-gray-800` di Tailwind.
- **Secondary Text**: `#64748B` ← `text-gray-500`.
- **Border**: `#E2E8F0` ← `border-gray-200`.
> **Catatan**: Jika token‑token di atas belum ada, tambahkan ke `tailwind.config.js` atau `resources/css/tokens.css` dan jalankan `npm run dev`/`npm run build`.

## 14. Komponen UI yang Digunakan
| Komponen | Path | Cara Pakai |
|----------|------|-----------|
| AntiSlopButton | `resources/js/components/AntiSlopButton.vue` | `<AntiSlopButton @tap="openCreateModal">Tambah Bahan</AntiSlopButton>` |
| StatusPill | `resources/js/components/StatusPill.vue` | `<StatusPill :type="row.status">{{ row.statusLabel }}</StatusPill>` |
| PageHeader | `resources/views/components/page-header.blade.php` | `@include('components.page-header', ['title' => 'Bahan Baku'])` |
> **Pastikan** komponen‑komponen tersebut memang ada di repo sebelum menambahkan referensi.

## 15. Lokasi Gambar & Asset
- Simpan screenshot & wireframe di `public/images/design/` (contoh: `public/images/design/anti-slop-home.png`).
- Tambahkan path pada markdown dengan link relatif, misalnya `![Home screenshot](../images/design/anti-slop-home.png)`.

## 16. Checklist Pengujian (contoh perintah)
```bash
# Pastikan filter, pagination, dan otorisasi berfungsi
php artisan test --filter=BahanBakuPageTest
# Jalankan semua tes UI (Laravel Dusk) jika tersedia
php artisan dusk --filter=BahanBaku
```
- Verifikasi bahwa **semua elemen UI** ter‑render dengan data nyata (tidak ada placeholder).
- Pastikan **warna kontras** ≥ 4.5:1 (bisa pakai `npm run lint:css` atau tools seperti `axe`).
- Pastikan **ukuran tap target** ≥ 48 dp pada perangkat seluler.

## 17. Versi Dokumen & Log Perubahan
**Versi:** 1.1 – 2026‑10‑28 – Menambahkan referensi token, daftar komponen, lokasi asset, checklist pengujian, dan header versi.

---
