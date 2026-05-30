export default function GasStationCard() {
	return (
		<div className="gas-station_card border border-base-100 bg-base-100 shadow-sm rounded-sm">
			<div className="card-body font-sans p-4">
				<h2 className="gas-station_card-title">Card title!</h2>
				<p>
					A card component has a figure, a body part, and inside body there are
					title and actions parts
				</p>
				<div className="divider divider-neutral my-1"></div>
				<p className="text-xs opacity-60 font-mono m-px">Lat: 50.7374</p>
				<p className="text-xs opacity-60 font-mono m-px">Lon: 7.0982</p>
			</div>
		</div>
	);
}
