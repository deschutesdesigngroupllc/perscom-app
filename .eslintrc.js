module.exports = {
  env: {
    browser: true,
    node: true,
  },
  globals: {
    route: "readonly"
  },
  parserOptions: {
    ecmaFeatures: {
      jsx: true,
    },
    ecmaVersion: 2018,
    sourceType: "module"
  },
  extends: [
    'eslint:recommended',
    'plugin:react/recommended',
    'prettier'
  ],
  rules: {
    'react/prop-types': 0
  },
  ignorePatterns: [
      "temp.js", "**/vendor/*.js"
  ],
  settings: {
    react: {
      version: 'detect'
    }
  }
}