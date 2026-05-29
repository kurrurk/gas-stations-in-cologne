import { __ } from "@wordpress/i18n";
import { useState, useEffect, useRef } from "@wordpress/element";

import pinBlue from "./pins/blue-pin.png";
import pinRed from "./pins/red-pin.png";

import Map from "ol/Map";
import View from "ol/View";
import TileLayer from "ol/layer/Tile";
import OSM from "ol/source/OSM";
import { fromLonLat } from "ol/proj";
import Style from "ol/style/Style";
import Icon from "ol/style/Icon";
import Text from "ol/style/Text";
import Fill from "ol/style/Fill";
import Stroke from "ol/style/Stroke";

import Feature from "ol/Feature";
import Point from "ol/geom/Point";
import VectorSource from "ol/source/Vector";
import VectorLayer from "ol/layer/Vector";
import { toLonLat } from "ol/proj";
import DragPan from "ol/interaction/DragPan";
import { defaults as defaultInteractions } from "ol/interaction";
import "ol/ol.css";

const defaultStyle = new Style({
	image: new Icon({
		src: pinBlue,
		scale: 0.04,
		anchor: [0.5, 1],
	}),
});

const hoverStyle = new Style({
	image: new Icon({
		src: pinRed,
		scale: 0.04,
		anchor: [0.5, 1],
	}),

	text: new Text({
		text: "test",
		offsetY: 30,
		padding: [6, 10, 6, 10],
		font: "13px sans-serif",
		fill: new Fill({
			color: "#fff",
		}),
		backgroundFill: new Fill({
			color: "rgba(0,0,0,0.7)",
		}),
		backgroundStroke: new Stroke({
			color: "#ff4444",
			width: 1,
		}),
	}),
});

/* ===== ADD / UPDATE MARKER ===== */
function addOrUpdatePost(post, vectorSource) {
	let feature = vectorSource.current.getFeatureById(post.id);

	const coords = fromLonLat([
		Number(post.meta?.["gas-station_geometry_x"] || 0),
		Number(post.meta?.["gas-station_geometry_y"] || 0),
	]);

	const iconUrl = pinBlue;

	feature = new Feature({
		geometry: new Point(coords),
	});

	feature.setId(post.id);

	feature.setStyle(defaultStyle);

	vectorSource.current.addFeature(feature);
}

export default function OpenLayersMap({ showMap, locations }) {
	const mapRef = useRef();
	const mapObj = useRef();
	const vectorSource = useRef(new VectorSource());
	const [showMapOverlay, setShowMapOverlay] = useState(false);
	const holdTimer = useRef(null);

	const markers = Array.isArray(locations) ? locations : [];

	markers.forEach((marker) => addOrUpdatePost(marker, vectorSource));

	/* ===== init ===== */

	useEffect(() => {
		if (!mapRef.current) return;
		if (mapObj.current) return;
		let hoveredFeature = null;

		const vectorLayer = new VectorLayer({
			source: vectorSource.current,
		});

		mapObj.current = new Map({
			target: mapRef.current,
			interactions: defaultInteractions({
				dragPan: false,
			}).extend([
				new DragPan({
					condition: (event) => {
						return event.originalEvent.ctrlKey && event.originalEvent.shiftKey;
					},
				}),
			]),
			layers: [
				new TileLayer({
					source: new OSM(),
				}),
				vectorLayer,
			],

			view: new View({
				center: fromLonLat([6.9603, 50.9375]),
				zoom: 10,
			}),
		});

		mapObj.current.on("pointermove", (event) => {
			const feature = mapObj.current.forEachFeatureAtPixel(
				event.pixel,
				(feature) => feature,
			);

			/* reset previous */

			if (hoveredFeature && hoveredFeature !== feature) {
				hoveredFeature.setStyle(defaultStyle);

				hoveredFeature = null;
			}

			/* hover current */

			if (feature) {
				feature.setStyle(hoverStyle);

				hoveredFeature = feature;
			}

			const viewport = mapObj.current.getViewport();

			const interactive =
				event.originalEvent.ctrlKey && event.originalEvent.shiftKey;

			viewport.classList.toggle("map-draggable", interactive);

			mapObj.current.on("movestart", () => {
				viewport.classList.add("map-dragging");
			});

			mapObj.current.on("moveend", () => {
				viewport.classList.remove("map-dragging");
			});
		});
	}, []);

	useEffect(() => {
		if (!mapRef.current) return;
		if (!mapObj.current) return;

		const observer = new ResizeObserver(() => {
			mapObj.current.updateSize();
		});

		observer.observe(mapRef.current);

		return () => observer.disconnect();
	}, []);

	/* ===== resize ===== */

	useEffect(() => {
		if (!showMap) return;
		if (!mapObj.current) return;

		setTimeout(() => {
			mapObj.current.updateSize();
		}, 100);
	}, [showMap]);

	/* ===== set Overlay ===== */

	const handlePointerDown = (e, holdTimer) => {
		const isInteractive = e.ctrlKey && e.shiftKey;

		if (isInteractive) return;

		holdTimer.current = setTimeout(() => {
			setShowMapOverlay(true);

			setTimeout(() => {
				setShowMapOverlay(false);
			}, 1500);
		}, 500);
	};

	const clearHoldTimer = (holdTimer) => {
		clearTimeout(holdTimer.current);
	};

	return (
		<div
			ref={mapRef}
			style={{ height: showMap ? "400px" : "0" }}
			className="rounded-sm mt-4 relative w-full overflow-hidden"
			onPointerDown={(e) => handlePointerDown(e, holdTimer)}
			onPointerUp={clearHoldTimer(holdTimer)}
			onPointerLeave={clearHoldTimer(holdTimer)}
		>
			{showMapOverlay && (
				<div
					className="
						absolute inset-0 z-50
						flex items-center justify-center
						bg-black/40
						text-white text-sm font-medium
						pointer-events-none
					"
				>
					Hold Ctrl + Shift to move the map
				</div>
			)}
		</div>
	);
}
