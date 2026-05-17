import { __ } from "@wordpress/i18n";
import { useState, useEffect, useRef } from "@wordpress/element";
import { useSelect } from "@wordpress/data";
import {
	useBlockProps,
	InspectorControls,
	BlockControls,
} from "@wordpress/block-editor";
import { TextControl, SelectControl, PanelBody } from "@wordpress/components";
import "./editor.scss";

import pinBlue from "./icons/blue-pin.png";
import pinRed from "./icons/red-pin.png";

import OpenLayersMap from "./components/Map/Map";

export default function Edit({ attributes, setAttributes }) {
	// Settings Fields
	const { columns, colorTheme, showMap } = attributes;
	const onChangeColumns = (newColumns) => {
		setAttributes({ columns: parseInt(newColumns, 10) });
	};
	const onChangeColorTheme = (newColorTheme) => {
		setAttributes({ colorTheme: newColorTheme });
	};
	const toggleShowMap = () => {
		setAttributes({ showMap: !showMap });
	};

	const [sortAddress, setSortAddress] = useState("");
	const [coords, setCoords] = useState("");
	const [search, setSearch] = useState("");
	const [sortBy, setSortBy] = useState("address");
	const [sortOrder, setSortOrder] = useState("asc");

	let debounceTimer;
	const handleSortAddressChange = (value) => {
		setSortAddress(value);

		clearTimeout(debounceTimer);
		debounceTimer = setTimeout(() => {
			fetchCoords(value);
		}, 500);
	};

	// Posts

	const posts = useSelect((select) =>
		select("core").getEntityRecords("postType", "gas-station", {
			per_page: -1,
		}),
	);

	const safePosts = Array.isArray(posts) ? posts : [];

	return (
		<>
			<InspectorControls>
				<PanelBody title="Layout">
					<SelectControl
						label="Columns"
						value={columns}
						options={[
							{ label: "1 column", value: 12 },
							{ label: "2 columns", value: 6 },
							{ label: "3 columns", value: 4 },
							{ label: "4 columns", value: 3 },
							{ label: "6 columns", value: 2 },
						]}
						onChange={onChangeColumns}
					/>
					<SelectControl
						label="Color Themes"
						value={colorTheme}
						options={[
							{ label: "Light", value: "light" },
							{ label: "Dark", value: "dark" },
							{ label: "Cupcake", value: "cupcake" },
							{ label: "Cyberpunk", value: "cyberpunk" },
							{ label: "Retro", value: "retro" },
							{ label: "Dracula", value: "dracula" },
							{ label: "Coffee", value: "coffee" },
						]}
						onChange={onChangeColorTheme}
					/>
				</PanelBody>
				<BlockControls
					controls={[
						{
							icon: "location-alt",
							title: __("Show Map", "gas-stations"),
							onClick: toggleShowMap,
							isActive: showMap,
						},
					]}
				></BlockControls>
			</InspectorControls>
			<div
				{...useBlockProps({
					className:
						"container bg-base-200 border-base-300 rounded-xl border border-base-300 bg-base-100 p-5 shadow-sm",
				})}
				data-theme={colorTheme}
			>
				{/* --- Settings Fields --- */}
				<fieldset className="fieldset">
					<TextControl
						label="Search by address"
						value={search}
						className="gas-stations-filter-form_text-field"
						S
						onChange={setSearch}
						placeholder="Enter address..."
					/>
					<SelectControl
						label="Sort by"
						value={sortBy}
						className="gas-stations-filter-form_select"
						options={[
							{ label: "Address", value: "address" },
							{ label: "Distance", value: "distance" },
							{ label: "ID", value: "id" },
						]}
						onChange={setSortBy}
					/>
					<SelectControl
						label="Order"
						value={sortOrder}
						className="gas-stations-filter-form_select"
						options={[
							{ label: "Ascending", value: "asc" },
							{ label: "Descending", value: "desc" },
						]}
						onChange={setSortOrder}
					/>
					{sortBy === "distance" && (
						<TextControl
							label="Address to calculate distance"
							value={sortAddress}
							className="gas-stations-filter-form_text-field"
							onChange={handleSortAddressChange}
							placeholder="Enter address..."
						/>
					)}
				</fieldset>
				<OpenLayersMap showMap={showMap} locations={posts} />
			</div>
		</>
	);
}
