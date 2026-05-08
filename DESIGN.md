---
name: The Library Design System
colors:
  surface: '#f7f9fb'
  surface-dim: '#d8dadc'
  surface-bright: '#f7f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f4f6'
  surface-container: '#eceef0'
  surface-container-high: '#e6e8ea'
  surface-container-highest: '#e0e3e5'
  on-surface: '#191c1e'
  on-surface-variant: '#3c4a42'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#6c7a71'
  outline-variant: '#bbcabf'
  surface-tint: '#006c49'
  primary: '#006c49'
  on-primary: '#ffffff'
  primary-container: '#10b981'
  on-primary-container: '#00422b'
  inverse-primary: '#4edea3'
  secondary: '#565e74'
  on-secondary: '#ffffff'
  secondary-container: '#dae2fd'
  on-secondary-container: '#5c647a'
  tertiary: '#515f74'
  on-tertiary: '#ffffff'
  tertiary-container: '#95a4bb'
  on-tertiary-container: '#2c3a4e'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#6ffbbe'
  primary-fixed-dim: '#4edea3'
  on-primary-fixed: '#002113'
  on-primary-fixed-variant: '#005236'
  secondary-fixed: '#dae2fd'
  secondary-fixed-dim: '#bec6e0'
  on-secondary-fixed: '#131b2e'
  on-secondary-fixed-variant: '#3f465c'
  tertiary-fixed: '#d5e3fd'
  tertiary-fixed-dim: '#b9c7e0'
  on-tertiary-fixed: '#0d1c2f'
  on-tertiary-fixed-variant: '#3a485c'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
typography:
  h1:
    fontFamily: newsreader
    fontSize: 40px
    fontWeight: '600'
    lineHeight: '1.2'
  h2:
    fontFamily: newsreader
    fontSize: 32px
    fontWeight: '500'
    lineHeight: '1.3'
  h3:
    fontFamily: newsreader
    fontSize: 24px
    fontWeight: '500'
    lineHeight: '1.4'
  body-lg:
    fontFamily: manrope
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: manrope
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  body-sm:
    fontFamily: manrope
    fontSize: 14px
    fontWeight: '400'
    lineHeight: '1.5'
  label-caps:
    fontFamily: manrope
    fontSize: 12px
    fontWeight: '700'
    lineHeight: '1'
    letterSpacing: 0.05em
  data-table:
    fontFamily: manrope
    fontSize: 14px
    fontWeight: '500'
    lineHeight: '1.4'
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  unit: 4px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 48px
  container-max: 1440px
  gutter: 24px
  margin: 32px
---

## Brand & Style
This design system is built upon the intersection of traditional literary authority and modern digital efficiency. It targets librarians, researchers, and institutional administrators who require a high-density information environment that remains calm and navigable. 

The aesthetic is a blend of **Minimalism** and **Corporate Modern**. It prioritizes structural integrity and clarity over decorative flair. The emotional goal is to evoke a sense of quiet focus—reminiscent of a physical library—while providing the high-performance tools of a contemporary SaaS platform. Generous negative space is utilized not just for aesthetics, but to reduce cognitive load during complex data management tasks.

## Colors
The palette is anchored by "Midnight Ink" (Secondary) and "Steel Archive" (Tertiary) deep blues to establish trust and stability. The primary action color is "Emerald Slate," a vibrant green used specifically for high-intent actions like adding new records, checking out books, or saving changes.

- **Primary:** Emerald Green (#10B981) for calls-to-action and success states.
- **Secondary:** Deep Navy (#0F172A) for navigation sidebar backgrounds and primary headers.
- **Tertiary:** Slate Blue (#334155) for secondary icons and supporting text.
- **Neutral:** A range of soft greys from #F8FAFC (backgrounds) to #E2E8F0 (borders).
- **Background:** The default workspace uses a very light cool grey to reduce eye strain compared to pure white.

## Typography
This design system utilizes a dual-font strategy to balance heritage with utility.

1.  **The Serif (newsreader):** Used for page titles, section headers, and book titles. Its literary character reminds the user of the physical objects they are managing.
2.  **The Sans-Serif (manrope):** Used for all interface elements, navigation menus, data tables, and input labels. It provides a crisp, geometric contrast to the serif and ensures readability at small scales in dense data views.

Data tables should use a slightly tighter line height and medium weight for the sans-serif to ensure columns are easily scannable.

## Layout & Spacing
The layout follows a **Fixed-Fluid Hybrid Grid**. The main navigation is a fixed-width sidebar (280px), while the content area utilizes a 12-column fluid grid that caps at 1440px to maintain line-length readability.

Spacing is based on a 4px baseline grid. Large-scale layouts should favor `xl` (48px) padding between major sections to emphasize the "clean and modern" requirement. Data-heavy tables and lists use "Compact" spacing (`sm` or 8px) to maximize information density without sacrificing clarity.

## Elevation & Depth
Depth in this design system is achieved through **Low-contrast outlines** and **Tonal layers** rather than heavy shadows.

- **Surfaces:** Use subtle shifts in background color (e.g., a white card on a #F8FAFC background) to define areas.
- **Borders:** All containers and cards use a 1px solid border in #E2E8F0. This creates a "structured archive" feel.
- **Shadows:** Use a single, extremely soft ambient shadow (0px 4px 20px rgba(15, 23, 42, 0.05)) only for floating elements like dropdown menus or active modals.
- **State Changes:** Hover states on interactive cards should transition from a light grey border to a Slate Blue border, rather than increasing shadow depth.

## Shapes
This design system employs a **Soft (0.25rem)** roundedness level. 

The choice of small border radii (4px for buttons/inputs, 8px for cards) maintains a professional and "organized" architectural feel. It avoids the playfulness of highly rounded "pill" shapes, opting instead for a precision-engineered look that aligns with institutional management software. Search bars and primary action buttons follow the standard `rounded` (4px) or `rounded-lg` (8px) rules consistently.

## Components
- **Buttons:** Primary buttons use a solid Emerald Green fill with white text. Secondary buttons use a transparent background with a Slate Blue border. Use all-caps for button labels in the sans-serif font to distinguish them from body text.
- **Inputs:** Text fields should have a subtle 1px border and a light grey background (#F1F5F9). On focus, the border changes to Emerald Green.
- **Cards:** Used for individual book records or dashboard widgets. Cards feature a white background, a 1px #E2E8F0 border, and no shadow.
- **Chips/Tags:** Used for "Available/Checked Out" statuses or genre categories. These should have a very subtle background tint of the status color (e.g., light green for available) with high-contrast text.
- **Data Tables:** Headers should have a light grey background (#F8FAFC) and use the `label-caps` typography style. Rows should use alternating zebra-striping or subtle 1px dividers.
- **Search Bar:** A prominent component featuring a "magnifying glass" icon and a soft grey placeholder text. It is the primary tool for library navigation.