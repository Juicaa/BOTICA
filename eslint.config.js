// eslint.config.js
export default [
  {
    files: ["**/*.js"],
    languageOptions: {
      ecmaVersion: 2021,
      sourceType: "module",
      globals: {
        window: "readonly",
        document: "readonly",
        fetch: "readonly",
        console: "readonly"
      }
    },
    rules: {
      semi: ["error", "always"],
      quotes: ["off"],
      "no-unused-vars": "warn",
      "no-undef": "error"
    }
  }
];
