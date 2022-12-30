#!/usr/bin/node

const path = require("path"),
    fsExtra = require("fs-extra"),
    rimraf = require("rimraf");

rimraf.sync(path.join(__dirname, "../build/"));

fsExtra.moveSync(path.join(__dirname, "dist/"), path.join(__dirname, "../build/"));
fsExtra.copySync(path.join(__dirname, "../api/"), path.join(__dirname, "../build/api/"));
