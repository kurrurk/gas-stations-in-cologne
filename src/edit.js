import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import './editor.scss';

export default function Edit() {
	return (
		<p { ...useBlockProps() }>
			{ __(
				'Gas Stations in Cologne – hello from the editor!',
				'gas-stations-in-cologne'
			) }
		</p>
	);
}
