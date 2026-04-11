```markdown
# Design System Strategy: High-End Academic Editorial

## 1. Overview & Creative North Star: "The Academic Curator"
This design system moves away from the sterile, "software-as-a-service" aesthetic and instead embraces the authoritative, tactile feel of a high-end academic journal. Our Creative North Star is **"The Academic Curator."** 

We are not building a simple database; we are building a platform of record. The interface must feel like a series of ivory vellum sheets layered upon an evergreen foundation. We break the "template" look by using intentional white space, bold editorial typography, and a "sidebar-as-anchor" layout that mimics the ChatGPT dashboard structure but elevates it with premium materiality.

## 2. Colors & Surface Philosophy
The palette is rooted in deep botanical greens and warm ivory tones, reflecting the prestige of Aldersgate College.

### The Color Logic
- **Primary (`#003616`) & Primary Container (`#1a4d2a`):** These are our "Forest" tones. They represent authority and the university’s heritage. Use them for the sidebar and primary actions.
- **Tertiary Fixed (`#ffdf95`) & Accent Yellow (`#f5c542`):** These "Golden Hour" tones are reserved for highlighting critical statuses and drawing the eye to primary CTAs.
- **Surface & Background (`#fff9ec` to `#ffffff`):** We never use pure white for large surfaces. The warmth of the ivory (`surface`) creates a high-end, paper-like feel that reduces eye strain.

### The "No-Line" Rule
**Borders are prohibited for sectioning.** To separate the sidebar from the main content or a list from a detail view, use a background shift. For example, the ChatGPT-style sidebar should use `primary` or `surface-container-high`, while the main workspace sits on the `surface` background.

### The Glass & Gradient Rule
To prevent the UI from feeling flat, use the **Signature Texture**: A subtle linear gradient from `primary` to `primary_container` on large interactive surfaces. For floating panels (like a new complaint modal), use **Glassmorphism**:
- **Background:** `surface` at 80% opacity.
- **Backdrop-blur:** 12px.
- **Result:** The ivory background "bleeds" through, creating a sense of sophisticated depth.

## 3. Typography: Editorial Authority
We utilize **Inter** to bridge the gap between modern tech and classic print.

- **Display & Headlines:** Use `display-md` or `headline-lg` for page titles. These must be **Bold** and have a tight letter-spacing (-0.02em). This is the "Editorial" voice.
- **Titles:** `title-lg` is for card headers and section titles. It acts as the anchor for the user’s eye.
- **Body:** `body-md` is the workhorse. Ensure a line-height of 1.5 for maximum readability in long complaint descriptions.
- **Labels:** `label-md` should be uppercase with a slight letter-spacing (+0.05em) when used for metadata or status tags.

## 4. Elevation & Depth: Tonal Layering
We do not use structural lines. We use **Physicality.**

- **The Layering Principle:** 
    1. Base: `surface` (The desk).
    2. Section: `surface-container-low` (The folder).
    3. Item/Card: `surface-container-lowest` (The paper).
- **Ambient Shadows:** For elements that must float (like a floating action button), use a shadow tinted with `primary`. 
    - `box-shadow: 0 10px 30px -5px rgba(0, 54, 22, 0.08);`
- **The "Ghost Border" Fallback:** If a container is placed on a background of the same color, use a `1px` stroke of `outline_variant` at **15% opacity**. It should be felt, not seen.

## 5. Components

### The Sidebar (ChatGPT-Style)
- **Background:** `primary` (`#003616`).
- **Active State:** A `primary_container` block with a `tertiary` (yellow) left-accent bar (4px).
- **Typography:** `label-md` in `on_primary`.

### Buttons
- **Primary:** Gradient from `primary` to `primary_container`. `0.5rem` (lg) roundedness. No border.
- **Secondary:** Transparent background with a "Ghost Border" of `primary`.
- **Tertiary:** `on_surface` text with no background; turns to `surface-container-high` on hover.

### Input Fields
- **Style:** Underline-only or subtle "soft box" using `surface-container-highest`.
- **Focus State:** The bottom border transforms into a 2px `primary` line. Background shifts slightly to `surface_bright`.

### Cards & Lists (No-Divider Policy)
- **Rule:** Never use a horizontal line to separate list items. 
- **Execution:** Increase vertical padding (`spacing-scale-lg`) and use a subtle `surface-container-low` hover state to define the row's boundaries.

### Status Chips
- **Pending:** `tertiary_container` with `on_tertiary_fixed_variant` text.
- **Resolved:** `secondary_container` with `on_secondary_container` text.
- **Urgent:** `error_container` with `on_error_container` text.

## 6. Do’s and Don’ts

### Do
- **Do** use the background gradient (`#fef3c7` to `#ffffff`) on the login screen to create a welcoming, prestigious atmosphere inspired by Aldersgate’s heritage.
- **Do** prioritize white space. If an element feels cramped, increase the padding rather than adding a border.
- **Do** use `display-sm` for "Empty State" messages to make the UI feel intentional and designed.

### Don’t
- **Don’t** use pure black (`#000000`) for text. Use `on_surface` or `primary` to keep the palette organic.
- **Don’t** use standard "Material Design" blue for links. Every interactive element must stay within the green/yellow/ivory ecosystem.
- **Don’t** use sharp 90-degree corners. Even a `0.25rem` (DEFAULT) radius is necessary to soften the academic tone and make it feel "Virtual."