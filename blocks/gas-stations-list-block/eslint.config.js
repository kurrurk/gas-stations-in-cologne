import js from "@eslint/js";
import globals from "globals";
import reactPlugin from "eslint-plugin-react";
import wpPlugin from "@wordpress/eslint-plugin";
import babelParser from "@babel/eslint-parser";

export default [
	{
		ignores: ["build/**", "node_modules/**"],
	},
	js.configs.recommended,

	{
		files: ["**/*.js", "**/*.jsx"],

		languageOptions: {
			parser: babelParser,

			parserOptions: {
				requireConfigFile: false,

				babelOptions: {
					presets: ["@babel/preset-react"],
				},
			},
			ecmaVersion: "latest",
			sourceType: "module",

			globals: {
				...globals.browser,
				...globals.node,
				wp: "readonly",
			},
		},

		plugins: {
			react: reactPlugin,
			"@wordpress": wpPlugin,
		},

		rules: {
			semi: ["error", "always"],
			quotes: ["error", "double"],
			"no-unused-vars": "warn",
		},
	},
];
