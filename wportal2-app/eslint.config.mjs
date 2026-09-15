import typescriptEslint from "@typescript-eslint/eslint-plugin";
import globals from "globals";
import tsParser from "@typescript-eslint/parser";
import path from "node:path";
import { fileURLToPath } from "node:url";
import js from "@eslint/js";
import { FlatCompat } from "@eslint/eslintrc";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const compat = new FlatCompat({
    baseDirectory: __dirname,
    recommendedConfig: js.configs.recommended,
    allConfig: js.configs.all
});

export default [{
    ignores: [
        "resources/script/app.tsx",
        "resources/script/Graphql/codegen/graphql.tsx",
    ],
}, ...compat.extends(
    "plugin:@typescript-eslint/recommended",
    "plugin:react/recommended",
), {
    plugins: {
        "@typescript-eslint": typescriptEslint,
    },

    languageOptions: {
        globals: {
            ...globals.node,
            ...globals.browser,
        },

        parser: tsParser,
        ecmaVersion: 5,
        sourceType: "commonjs",

        parserOptions: {
            "sourceType\"": "module",

            ecmaFeatures: {
                jsx: true,
            },
        },
    },

    settings: {
        "import/resolver": {
            node: {
                extensions: [".ts", ".tsx"],
            },
        },
        "react": {
            version: "detect",
        }
    },

    rules: {
        "no-shadow": "off",
        "no-unused-expressions": "off",
        "no-use-before-define": "off",
        "@typescript-eslint/no-unused-expressions": "error",
        "@typescript-eslint/explicit-function-return-type": "off",
        "@typescript-eslint/no-non-null-assertion": "off",

        "@typescript-eslint/no-unused-vars": ["error", {
            argsIgnorePattern: "^_",
        }],

        "@typescript-eslint/no-explicit-any": "warn",
        "@typescript-eslint/camelcase": "off",
        "prettier/prettier": "off",
        "import/prefer-default-export": "off",
        "import/no-default-export": "off",
        "import/no-duplicates": "off",
        "import/extensions": "off",
        "import/unresolved": "off",
        "import/no-unresolved": "off",
        "import/no-extraneous-dependencies": "off",

        "react/jsx-filename-extension": ["error", {
            extensions: [".jsx", ".tsx"],
        }],

        "react/destructuring-assignment": ["error", "always", {
            ignoreClassFields: true,
        }],

        "react/prop-types": "off",
        "react/jsx-one-expression-per-line": "off",
        "react/jsx-wrap-multilines": "off",
        "react/require-default-props": "off",
        "max-classes-per-file": ["error", 2],
        "@typescript-eslint/no-empty-interface": "off",

        "@typescript-eslint/naming-convention": ["error", {
            selector: "interface",
            format: ["PascalCase"],

            custom: {
                regex: "^I[A-Z]",
                match: true,
            },
        }],

        "no-plusplus": ["error", {
            allowForLoopAfterthoughts: true,
        }],

        curly: ["error", "all"],
        "react/function-component-definition": 0,

        "react/jsx-no-useless-fragment": ["error", {
            allowExpressions: true,
        }],

        "@typescript-eslint/no-empty-object-type": "off",
    },
}];
