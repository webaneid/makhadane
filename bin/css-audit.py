#!/usr/bin/env python3
"""
Build & Audit CSS Makhad Ane (bin/css-audit.py)
1. Compile/minify css/main.css -> css/main.min.css
2. Verifikasi keseimbangan kurung kurawal CSS ({ == })
3. Deteksi class/ID mati di css/main.css (sections [03]-[12]) yang tidak dipakai di PHP/JS.
"""
from pathlib import Path
import re
import sys

ROOT = Path(__file__).resolve().parent.parent
MAIN_CSS = ROOT / "css" / "main.css"
MAIN_MIN = ROOT / "css" / "main.min.css"

ALLOWED_DYNAMIC_CLASSES = {
    # WordPress Core body_class, post_class, widgets, menus, comments, Gutenberg blocks
    "single", "single-post", "page", "archive", "category", "tag", "author", "search", "search-results",
    "error404", "home", "blog", "logged-in", "admin-bar",
    "page-template-page-home", "page-template-page-kontak", "page-template-page-newslist",
    "alignleft", "alignright", "aligncenter", "alignwide", "alignfull",
    "wp-caption", "wp-caption-text", "gallery-caption", "bypostauthor", "sticky",
    "size-full", "size-large", "size-medium", "size-thumbnail",
    "screen-reader-text", "custom-logo-link",
    "wp-block-image", "wp-block-table", "wp-block-quote", "wp-block-Pullquote",
    "wp-block-button__link", "wp-block-gallery", "blocks-gallery-grid", "blocks-gallery-item",
    "wp-block-group", "wp-block-group__inner-container", "wp-block-cover", "wp-block-embed",
    "wp-block-embed__wrapper", "wp-block-file", "wp-block-file__button", "wp-block-latest-posts",
    "wp-block-yoast-faq-block", "schema-faq-section", "has-background", "active-row",
    "wpcf7-form", "wpcf7-submit",
    "widget", "widget_search", "widget_categories", "widget_archive", "widget_recent_entries",
    "widget_recent_comments", "widget_meta", "widget_pages", "widget_nav_menu", "widget_text",
    "widget_media_image", "widget_media_gallery", "widget_media_video", "widget_block", "widget_calendar", "widget_rss",
    "cat-item", "tagcloud", "calendar_wrap", "wp-calendar", "wp-calendar-table", "rsswidget", "rss-date", "rssSummary",
    "post-date", "recentcomments", "comment-author-link",
    "wp-block-heading", "wp-block-categories-list", "wp-block-archives-list", "wp-block-latest-comments",
    "wp-block-latest-comments__comment", "wp-block-latest-comments__comment-meta", "wp-block-latest-comments__comment-date",
    "wp-block-latest-posts__post-date", "wp-block-latest-posts__featured-image",
    "wp-block-search", "wp-block-search__inside-wrapper", "wp-block-search__input", "wp-block-search__button", "wp-block-search__label",
    "wp-block-tag-cloud", "wp-block-calendar", "wp-block-video",
    "gallery", "gallery-item", "gallery-icon",
    "wp-block-separator", "wp-block-pullquote", "has-nested-images", "wp-embed-aspect-16-9", "is-type-video",
    "wp-block-buttons", "wp-block-button", "is-style-outline", "wp-block-details", "schema-faq-answer", "individual-image",
    "wp-block-media-text", "wp-block-media-text__media", "wp-block-media-text__content", "has-large-font-size",
    # ACF dynamic classes (tp/section/big-slider.php & tp/section/image-side-text.php: ane_posisi)
    "align-content-start", "align-content-center", "align-content-end",
    "gambar-kiri", "gambar-kanan",
    "menu-item", "menu-item-has-children", "sub-menu", "current-menu-item", "page_item",
    "comment", "comment-list", "comment-body", "comment-meta", "comment-author", "comment-content",
    "comment-respond", "comment-form", "comment-reply-title", "comments-area", "children",
    "nav-links", "page-numbers", "prev", "next", "current", "dots",
    # JS Libraries (Owl Carousel, Magnific Popup, MeanMenu)
    "owl-carousel", "owl-stage", "owl-stage-outer", "owl-item", "owl-nav", "owl-prev", "owl-next", "owl-dots", "owl-dot",
    "owl-loaded", "owl-loading", "owl-hidden", "owl-refresh", "owl-drag", "owl-grab", "disabled", "active",
    "mfp-bg", "mfp-wrap", "mfp-container", "mfp-content", "mfp-figure", "mfp-close", "mfp-img", "mfp-bottom-bar", "mfp-title", "mfp-counter",
    "mean-container", "mean-bar", "mean-nav", "meanmenu-reveal", "mean-expand", "mean-clicked", "mean-push", "mean-remove", "mean-last", "wrapper",
    "lazyloaded",
}


def minify_css(text: str) -> str:
    text = re.sub(r"/\*[\s\S]*?\*/", "", text)
    text = re.sub(r"\s+", " ", text)
    text = re.sub(r"\s*([{};:,>~])\s*", r"\1", text)
    text = text.replace(";}", "}")
    return text.strip()


def main() -> int:
    css_text = MAIN_CSS.read_text(encoding="utf-8")

    # 1. Check brace balance
    open_b = css_text.count("{")
    close_b = css_text.count("}")
    if open_b != close_b:
        print(f"❌ ERROR: Kurung kurawal di css/main.css tidak seimbang ({open_b} '{{' vs {close_b} '}}')!")
        return 1

    # 2. Compile css/main.min.css
    minified = minify_css(css_text)
    MAIN_MIN.write_text(minified + "\n", encoding="utf-8")

    # 3. Audit dead selectors in sections [03]..[12]
    code_text = ""
    for p in (
        list(ROOT.glob("*.php"))
        + list((ROOT / "tp").rglob("*.php"))
        + list((ROOT / "inc").rglob("*.php"))
        + list((ROOT / "js").rglob("*.js"))
    ):
        code_text += "\n" + p.read_text(encoding="utf-8", errors="ignore")

    lines = css_text.splitlines()
    start_idx = next((i for i, l in enumerate(lines) if l.strip().startswith("[03]")), 305)

    dead_rules = []
    for idx in range(start_idx, len(lines)):
        s = lines[idx].strip()
        if s.endswith("{") and not s.startswith("@") and not s.startswith("/*"):
            sel = s[:-1].strip()
            tokens = re.findall(r"[.#]([a-zA-Z_][a-zA-Z0-9_-]*)", sel)
            missing = [t for t in tokens if (t not in code_text) and (t not in ALLOWED_DYNAMIC_CLASSES)]
            if missing:
                dead_rules.append((idx + 1, sel, missing))

    if dead_rules:
        print(f"❌ Ditemukan {len(dead_rules)} rule CSS dengan class/ID yang tidak dipakai di PHP/JS:")
        for line_no, sel, missing in dead_rules:
            print(f"  - Baris {line_no}: {sel} (tidak ditemukan: {', '.join(missing)})")
        print("Hapus CSS class yang sudah tidak dipakai agar css/main.min.css tetap ringan.")
        return 1

    print(
        f"✅ CSS Audit & Build LOLOS: css/main.css ({len(lines)} baris) -> "
        f"css/main.min.css ({len(minified)/1024:.1f} KB), 0 dead CSS rules."
    )
    return 0


if __name__ == "__main__":
    sys.exit(main())
