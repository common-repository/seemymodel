/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from "@wordpress/blocks";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./style.scss";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";
import { __experimentalUnitControl as UnitControl } from "@wordpress/components";

import { __ } from "@wordpress/i18n";

import apiFetch from "@wordpress/api-fetch";

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType("see-my-model/group-viewer", {
	attributes: {
		folder_access_key: { type: "string" },
		folders_loaded: { type: "boolean", default: false },
		folders: { type: "array" },
		unauthorized_user: { type: "boolean" },
		width: { type: "string", default: "400px" },
		height: { type: "string", default: "400px" },
		language: { type: "string", default: "auto" },
	},

	edit: ({ attributes, setAttributes }) => {
		if (attributes.folders_loaded === false) {
			apiFetch({ path: "see-my-model/folders" }).then(
				(result) => {
					setAttributes({ folders_loaded: true });
					setAttributes({ folders: result });
					setAttributes({ folder_access_key: result[0]?.id });
				},
				(error) => {
					setAttributes({ unauthorized_user: true });
					console.log(error);
				}
			);
		}

		const onFolderChange = (event) => {
			setAttributes({ folder_access_key: event.target.value });
		};

		const onWidthChange = (value) => {
			setAttributes({ width: value });
		};

		const onHeightChange = (value) => {
			setAttributes({ height: value });
		};

		const onLanguageChange = (event) => {
			setAttributes({ language: event.target.value });
		};

		let unauthorized_error;
		if (attributes.unauthorized_user) {
			unauthorized_error = (
				<p>
					{__("Authorization error.", "see-my-model")}

					<a href="options-general.php?page=seemymodel-options" target="_blank">
						{__(" Click here", "see-my-model")}
					</a>
					{__("to log into your seemymodel.com account.", "see-my-model")}
				</p>
			);
		}
		let selectModel = (
			<fieldset>
				<legend className="blocks-base-control__label">
					{__("Choose your folder", "see-my-model")}
				</legend>
				<select
					name="folders"
					value={attributes?.folder_access_key}
					onChange={onFolderChange}
				>
					{attributes?.folders?.map((folder) => (
						<option value={folder.id}>{folder.name}</option>
					))}
				</select>

				<legend className="blocks-base-control__label">
					{__("Width", "see-my-model")}
				</legend>
				<UnitControl onChange={onWidthChange} value={attributes?.width} />

				<legend className="blocks-base-control__label">
					{__("Height", "see-my-model")}
				</legend>
				<UnitControl onChange={onHeightChange} value={attributes?.height} />

				<legend className="blocks-base-control__label">
					{__("Language", "see-my-model")}
				</legend>
				<select
					name="language"
					value={attributes?.language}
					onChange={onLanguageChange}
				>
					<option value="auto">{__("Auto", "see-my-model")}</option>
					<option value="pl">{__("Polish", "see-my-model")}</option>
					<option value="en">{__("English", "see-my-model")}</option>
				</select>
			</fieldset>
		);
		return (
			<div {...useBlockProps()}>
				<InspectorControls key="setting">
					<div id="gutenpride-controls">
						{unauthorized_error ? unauthorized_error : selectModel}
					</div>
				</InspectorControls>
				<see-my-model-group
					folder-access-key={attributes.folder_access_key}
					locale={attributes.language !== "auto" ? attributes.language : ""}
					style={{
						width: attributes.width,
						height: attributes.height,
					}}
				></see-my-model-group>
			</div>
		);
	},

	save: ({ attributes }) => {
		return (
			<div {...useBlockProps.save()}>
				<see-my-model-group
					folder-access-key={attributes.folder_access_key}
					locale={attributes.language !== "auto" ? attributes.language : ""}
					style={{
						width: attributes.width,
						height: attributes.height,
					}}
				></see-my-model-group>
			</div>
		);
	},
});
