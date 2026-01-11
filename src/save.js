import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

export default function save() {
	return (
		<p { ...useBlockProps.save() }>
			{ __(
				'Gas Stations in Cologne – hello from the saved content!',
				'gas-stations-in-cologne'
			) }
		</p>
	);
}
