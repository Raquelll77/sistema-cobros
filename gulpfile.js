import { src, dest, watch, series } from "gulp";
import * as dartSass from "sass";
import gulpSass from "gulp-sass";
import terser from "gulp-terser";

const sass = gulpSass(dartSass);

const paths = {
  scss: "src/scss/**/*.scss",
  js: "src/js/**/*.js",
};

export function css() {
  return src(paths.scss, { sourcemaps: true })
    .pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError))
    .pipe(dest("./public/build/css", { sourcemaps: "." }));
}

export function js() {
  return src(paths.js).pipe(terser()).pipe(dest("./public/build/js"));
}

export function dev() {
  watch(paths.scss, css);
  watch(paths.js, js);
}

export default series(js, css, dev);
