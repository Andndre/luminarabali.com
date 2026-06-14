# luminarabali.com - UI/UX Design System & Guidelines for AI Agents

This document defines the visual aesthetics, styling conventions, and user experience guidelines for **luminarabali.com**. All future AI agents modifying or generating user interfaces for this codebase **must** strictly adhere to these design specifications to ensure a premium, modern, and visually consistent experience.

---

## 1. Core Design Philosophy

The interface of **luminarabali.com** must never look generic, plain, or standard-bootstrap. It must feel premium, alive, and polished.

*   **Wow Factor at First Glance**: Use vibrant modern color palettes, sleek dark modes, subtle glassmorphism, soft glow effects, and modern typography instead of browser default styles.
*   **Dynamic and Interactive**: Every interactive element (buttons, cards, links, inputs) must react to user inputs with smooth micro-animations, hover scaling, and clear state transitions.
*   **Division-Scoped Aesthetics**: Visual styling must adjust based on the business division context (`photobooth` vs. `visual`) to reinforce brand identity.

---

## 2. Color System & Palettes

Avoid using generic primaries (e.g., solid red, basic blue, bright green). Use carefully curated color pairs and smooth gradients.

### A. General Base System
*   **Dark Backgrounds**: `bg-slate-950` as the absolute background, layered with `bg-slate-900` for cards and content blocks.
*   **Light Backgrounds**: `bg-slate-50` base, with white (`bg-white`) containers.
*   **Borders & Dividers**: Subtle borders using `border-slate-800` (dark mode) or `border-slate-200` (light mode).

### B. Division Color Segregation
When rendering components or pages, apply these specific colors to establish visual hierarchy:

| Division | Primary Hue | Gradient Range | Hover / Accent Glow | Brand Representation |
| :--- | :--- | :--- | :--- | :--- |
| **Photobooth** | Indigo / Violet | `from-indigo-600 to-violet-600` | `shadow-indigo-500/20` | Dynamic, fun, high-tech, camera flash |
| **Visual** | Amber / Emerald | `from-amber-500 to-emerald-600` | `shadow-amber-500/20` | Warmth, elegance, cinematic, storytelling |
| **Super Admin**| Indigo / Slate | Solid `#4F46E5` | `shadow-indigo-500/10` | Enterprise, robust control, security, tech |

---

## 3. Typography & Hierarchy

Always load modern web typography from Google Fonts:
*   **Public/Consumer Pages**: **Inter**, **Outfit**, or **Plus Jakarta Sans**.
*   **Admin/SaaS Pages**: **Fira Sans** (UI) and **Fira Code** (Code Editor).
*   **Headings (`h1`, `h2`, `h3`)**: Use `font-sans font-bold tracking-tight text-slate-900 dark:text-white`. For landing pages, headings should use gradients:
    ```html
    <h1 class="text-4xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-400 via-violet-400 to-pink-500 bg-clip-text text-transparent">
        Title Here
    </h1>
    ```
*   **Body Text**: Standard font weight `font-normal text-slate-600 dark:text-slate-300 leading-relaxed`.
*   **Labels & Buttons**: `font-medium tracking-wide uppercase text-xs`.

---

## 4. Layout, Cards & Glassmorphism

To achieve a modern "premium app" feel, prioritize glassmorphism for floating overlays, navbars, and dashboard cards.

### Glassmorphism Card Formula:
```html
<div class="backdrop-blur-md bg-white/10 dark:bg-slate-900/40 border border-white/20 dark:border-slate-800/60 rounded-2xl shadow-xl">
    <!-- Card Content -->
</div>
```

### Card Elevation & Shadowing:
*   Instead of standard gray shadows, use subtle colored ambient dropshadows:
    *   *Photobooth Context*: `shadow-xl shadow-indigo-500/5 hover:shadow-indigo-500/10`
    *   *Visual Context*: `shadow-xl shadow-amber-500/5 hover:shadow-amber-500/10`
*   Use rounded corners of `rounded-2xl` (1rem / 16px) or `rounded-3xl` (1.5rem / 24px) for cards to give a modern, soft aesthetic.

---

## 5. Admin SaaS & Editor Aesthetics

The administrative control panels (e.g., Component Library, Editors) follow the **Minimalism & Swiss Style** to optimize data density and readability.

*   **Colors**: Dominant use of clean whitespace (`bg-white` and `bg-gray-50`) with thin, crisp borders (`border-gray-200`). Primary actions use `bg-indigo-600` (`#4F46E5`).
*   **Typography**: Utilize **Fira Sans** for the dashboard text and **Fira Code** for all monospaced elements (code editors, variable names, syntax).
*   **Layout Structure**: Avoid unnecessary wrapping margins or massive drop shadows. Use edge-to-edge layouts for tools (e.g., full height `h-full flex` without padded `<main>`) to maximize screen real estate.
*   **Focus & Interaction**: Fast, snappy transitions (150ms). Focus rings should be clearly defined: `focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500`.
*   **Cards**: Flat or very subtle shadows (`shadow-sm`) instead of the heavy glassmorphism used in public-facing pages.

---

## 6. Micro-Animations & Interaction States

An interface that feels alive encourages interaction. Follow these rules for all hover states:

### A. Buttons & Link Elements
*   **Always** include `transition-all duration-300 ease-in-out` on interactive components.
*   **Scale Hover**: Add a soft scaling factor to primary actions on hover:
    `hover:scale-[1.02] active:scale-[0.98]`
*   **Glow Effects**:
    ```html
    <button class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-xl font-semibold shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]">
        Book Now
    </button>
    ```

### B. Form Inputs
*   Never use standard browser focus rings. Use consistent colored focus highlights matching the division:
    *   *Active Border*: `focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20`
    *   *Transitions*: Apply transitions on borders and ring outlines to make focusing feel smooth.

---

## 6. SweetAlert2 Custom Configurations

When utilizing **SweetAlert2** for administrative actions (deletions, warnings, success flows), match the background and button styles with our core design system. Do **not** use default SWAL layouts.

### Brand SWAL Theme Configuration Pattern:
```javascript
Swal.fire({
    title: 'Konfirmasi Tindakan',
    text: 'Apakah Anda yakin ingin memproses data ini?',
    icon: 'warning',
    background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff',
    color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#0f172a',
    showCancelButton: true,
    confirmButtonText: 'Ya, Lanjutkan',
    cancelButtonText: 'Batal',
    customClass: {
        confirmButton: 'px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-semibold mx-2 hover:bg-indigo-700 transition-colors',
        cancelButton: 'px-5 py-2.5 rounded-lg bg-slate-600 text-white font-semibold mx-2 hover:bg-slate-700 transition-colors',
        popup: 'rounded-2xl border border-slate-200 dark:border-slate-800'
    },
    buttonsStyling: false
});
```

---

## 7. Responsive Layout Grid Principles

*   **Mobile-First Approach**: All user views (particularly public landing pages, pricelists, booking forms, and linktree portals) must be 100% responsive and optimized for mobile touch inputs.
*   **Navigation Bar**: Administrative navigation must automatically convert to an overlay slide-out menu or a floating bottom navigation block on mobile screens.
*   **Grids**: Grid templates must scale seamlessly:
    `grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6`

---

## 8. Frontend Assets & Image Handlers

*   **Loading Indicators**: Form submissions and media generation must render a loading skeleton state instead of blocking the entire screen with raw spinners.
*   **Optimized Thumbnails**: Images rendered inside client portals must display centered in containers with proper scaling bounds (`object-cover`) to prevent squishing or layout distortion.
