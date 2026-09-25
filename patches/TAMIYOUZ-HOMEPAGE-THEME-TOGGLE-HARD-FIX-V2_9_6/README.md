# TAMIYOUZ-HOMEPAGE-THEME-TOGGLE-HARD-FIX-V2_9_6

TITLE=Homepage Reference Lock
VERSION=V2.9.6
SCOPE=Theme Toggle Hard Fix

## Symptom
- home.js is now injected
- theme CSS exists
- user still cannot visibly switch Dark → Light

## Fix
This isolated MU-plugin makes the toggle deterministic:
- capture-phase click handler on [data-tyz-theme-toggle]
- prevents old/duplicate theme listeners from toggling twice
- directly sets html[data-tyz-theme]
- persists localStorage key tamiyouz-theme
- updates meta theme-color
- dispatches tamiyouz:v21theme for canvas redraw

It does NOT change homepage layout, template, CSS, content, or DB.

## Verification
A browser interaction is required:
1. start in dark
2. click toggle once → html[data-tyz-theme]=light
3. body/background visibly becomes light
4. localStorage tamiyouz-theme=light
5. click again → dark
