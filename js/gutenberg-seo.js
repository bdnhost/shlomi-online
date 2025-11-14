/**
 * Gutenberg SEO Panel for Shlomi Online
 */

(function () {
  const { registerPlugin } = wp.plugins;
  const { PluginDocumentSettingPanel } = wp.editPost;
  const { TextareaControl, TextControl } = wp.components;
  const { withSelect, withDispatch } = wp.data;
  const { compose } = wp.compose;
  const { createElement: el } = wp.element;

  const ShlomiSEOPanel = compose([
    withSelect((select) => {
      const { getEditedPostAttribute } = select("core/editor");
      return {
        seoDescription:
          getEditedPostAttribute("meta")["_seo_description"] || "",
        seoKeywords: getEditedPostAttribute("meta")["_seo_keywords"] || "",
      };
    }),
    withDispatch((dispatch) => {
      const { editPost } = dispatch("core/editor");
      return {
        updateSEODescription: (value) => {
          editPost({ meta: { _seo_description: value } });
        },
        updateSEOKeywords: (value) => {
          editPost({ meta: { _seo_keywords: value } });
        },
      };
    }),
  ])((props) => {
    return el(
      PluginDocumentSettingPanel,
      {
        name: "shlomi-seo-panel",
        title: "הגדרות SEO - שלומי אונליין",
        className: "shlomi-seo-panel",
      },
      [
        el(TextareaControl, {
          key: "seo-description",
          label: "תיאור SEO (Meta Description)",
          help: "תיאור קצר של הכתבה (150-160 תווים מומלץ)",
          value: props.seoDescription,
          onChange: props.updateSEODescription,
          rows: 3,
        }),
        el(TextControl, {
          key: "seo-keywords",
          label: "מילות מפתח",
          help: "מילות מפתח מופרדות בפסיקים",
          value: props.seoKeywords,
          onChange: props.updateSEOKeywords,
        }),
      ]
    );
  });

  registerPlugin("shlomi-seo-panel", {
    render: ShlomiSEOPanel,
  });
})();
