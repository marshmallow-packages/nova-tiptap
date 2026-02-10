# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [6.0.0] - 2025-02-10

### Changed
- Upgraded TipTap from v2 beta (`^2.0.0-beta.83`) to v3 stable (`^3.0.0`)
- Replaced Font Awesome with custom SVG icons
- Updated axios from v0.21 to v1.7
- Updated lowlight from v2 to v3 (with new `createLowlight` API)
- Renamed `History` extension to `UndoRedo` (TipTap v3 requirement)
- Consolidated TipTap extension imports:
  - List extensions now imported from `@tiptap/extension-list`
  - Table extensions now imported from `@tiptap/extension-table`
  - Core extensions now imported from `@tiptap/extensions`
  - BubbleMenu imported from `@tiptap/vue-3/menus`
- Now using `ImageResize` extension directly instead of separate `Image` extension for better resize support
- Updated table icon with new outline-style SVG
- Updated RTL icon with improved design
- Redesigned toolbar with TipTap-style UI:
  - Gray icons by default, darker on hover
  - Centered button alignment
  - Lighter borders integrated with editor content
  - Cleaner, more minimal design
- Modernized all modal designs with new styling

### Added
- **Bubble Menu**: Floating toolbar appears on text selection
  - Configurable via `->bubbleMenuButtons(['bold', 'italic', 'link', 'color'])`
  - Supports: bold, italic, strike, underline, code, highlight, subscript, superscript, link, color, backgroundColor
  - Can be disabled with `->withoutBubbleMenu()`
- **Dropdown Menus**: New grouped button options
  - `headingDropdown` - H1-H6 and paragraph in dropdown
  - `listDropdown` - Bullet and ordered lists in dropdown
  - `alignDropdown` - Text alignment options in dropdown
- **Inline Link Editor**: Quick link editing dropdown in toolbar
  - Paste URL and apply with enter
  - Open, edit, and remove links
  - "Advanced options" opens full modal
- **Drag & Drop Image Upload**: Drop images directly into editor content
- **Paste Image Support**: Paste images from clipboard
- **Image Upload Node**: `imageUpload` button inserts a TipTap-style drop zone
  - Click to upload or drag and drop interface
  - Upload progress indicator
  - Automatically converts to image after upload
- New custom SVG icon system in `resources/js/components/icons/`
- `TiptapIcon` component for unified icon handling
- `@floating-ui/dom` dependency (required for TipTap v3)
- `@tiptap/extension-file-handler` for drag & drop support
- `@tiptap/extension-bubble-menu` for floating toolbar
- `IframeComponent.vue` for iframe node view rendering
- `ImageUploadNode.js` extension and `ImageUploadNodeView.vue` component
- CSS styles for bubble menu, dropdowns, modals, and toolbar

### Removed
- Font Awesome dependency (all `@fortawesome/*` packages)
- Unused dependencies: `vue-trix`, `add`, `i`
- Legacy TipTap v1 API code in `iframe.js`
- Separate `@tiptap/extension-image` import (now using `ImageResize` extension)
- Separate unlink button (now integrated into link dropdown)

### Fixed
- Rewrote `iframe.js` extension using TipTap v3 `Node.create()` API
- Updated all Vue components to use new icon system
- Image resize handles now persist after saving and reloading the page
- Image editing modal now properly detects and updates existing images
- Fixed image attribute updates (title, alt, class) for existing images

### Migration
See the "Upgrading to v6.0" section in UPGRADE-GUIDE.md for detailed migration instructions.

## [5.x.x] - Previous Versions

See previous release notes for earlier versions.
