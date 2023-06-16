# Commits

feat(frontend/src/): added postComment() & feedback actions + bugfix
fix(database|backend|frontend): feature, bugfix, performance and ui, ux
feat(videodata.svelte|videosection.svelte): improved ux, added fetchNextVideo function to buttons
feat(src/lib/components/videoinfoinputfields.svelte): implemented cross-site scripting protection
fix(frontend/src/): updated ui, introduced new color-theme
style(frontend/src/): code comments and style
Merge branch 'master' of https://github.com/jonasfroeller/SvelteKit_VidSlide
feat(videoinfoinputfields.svelte): added i18n for form-validation
refactor(src/routes/api/api-routes.js): added Route object to improve fetching the rest-api
feat(frontend/src/): added EditVideo UploadVideo & VideoInfoInputFields + 1 popup & 1 toast
build(tailwind.config.cjs|package.json): updated dependencies
fix(src/lib/components/userdata.svelte): allow account edit only if account is own
docs(readme.md): added gh-pages docs badge
feat(backend/db_api.php): added post video feedback api endpoint
test(index.test.js): updated post video feedback and added post comment feedback test
feat(db_api.php): added posting comments and liking comments
refactor(frontend/src/lib/components): added missing html lang attribute
docs(readme.md): added database init
feat(userdata.svelte): added profile edit feat
feat(backend/db_api.php|frontend/src/): added unfollow feat
perf(backend/db_api.php|frontend/src/): removed unnecessary and complicated JSON parsing
feat(backend/db_api.php|frontend/src/): added follow feat
fix(frontend/components): fixed display of likes/dislikes and hashtags on small vids
fix(backend/db_api.php|frontend/api.js&index.test.js): fixed POST, PUT, DELETE REST-API endpoints
fix(backend/db_api.php|frontend/src/): fetching tags & follower bugfix, avatar ui, search feat
feat(backend/db_api.php|frontend/src/): code docu/types, feat stay logged in, ui, bugfix fetching
fix(frontend): deps, ui, bugfix, chore
docs(backend/db_api.php|frontend/index.test.js): api-options: code-docu & tests
docs(backend/db_api.php): api-options: code-docu
fix(backend/db_api.php): fixed auth, added code docu & next step TODOs
test(vitest test:unit index.test.js): post-api-tests: auth(), postVideo(), signout()
test(vitest test:unit index.test.js): get-api-tests
fix(frontend): fixed replaceLocaleInUrl(), signUp.svelte & +error.svelte + updated site.webmanifest
docs(readme.md): added commitizen friendly badge
chore(package.json): added commitizen package
cfgs & tests
input placeholder i18n & setup_env + example.env
deps & popup bug-fix & sidebar sm & input-val i18n
pushed backend docker-image to dockerhub
frontend meta & goto forward
styling && docs
backend security
user data fetch
bugfix
dev follower loading bugfix && delete scripts
merged pull request #1 from jonasfroeller/imgbot
[ImgBot] Optimize images
docs styling fix
docs.yaml
nojekyll :/
mv .nojekyll
.nojekyll
workflow
docs
gh-action
link css fix
include css and images in docs
gh-pages fix
upload
deps
renamed tables and attributes
gitignore
todo
README
fixed build issue
frontend & auth vars
frontend code reduction
ui++
ui
likes & dislikes
page transition
frontend home
Merge branch 'master' of https://github.com/jonasfroeller/SvelteKit_VidSlide
docs
deleted database migrations
deleted database backups
get api & home content load (no scroll yet)
backend api & frontend
docker & api & docs
database & backend & frontend login + api
api working 0.0.0
api update
database tables
account & settings pages wireframe
changed adapter
home & search pages wireframe
backend & database + frontend connection idea
added wireframe
added build outdir
app shell & basic functionality