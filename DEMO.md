# Live Demo Runbook — GitHub Workflow & PR Code Review

ကြာချိန် **၁၅ မိနစ်**။ terminal font ကို ကြီးကြီးထားပါ (၁၈pt+)၊ browser zoom ၁၂၅%။

---

## Talk မတက်ခင် (တစ်ခါတည်း)

```bash
cd ~/Documents/dev-talk/pr-workflow-demo
composer install
./vendor/bin/pest              # အားလုံး အစိမ်း ဖြစ်ရမယ်
./scripts/setup-github.sh      # repo ဆောက် + branch protection ဖွင့်
```

> ⚠️ CI က GitHub Actions ပေါ်မှာ ~၄၀ စက္ကန့်လောက်ကြာတယ်။ demo မှာ စောင့်ရင်း
> ပရိသတ်ကို "ခုစောင့်ရတဲ့ ၄၀ စက္ကန့်က၊ production မှာ bug ရှာရမယ့် ၄ နာရီကို ကယ်တာ" လို့ ပြောပါ။

**၂-၃ ခါ လေ့ကျင့်ပါ။** ကြားထဲ ပြန်စချင်ရင် — `./scripts/reset-demo.sh`

> ### ⚠️ Approval အကြောင်း — မဖြစ်မနေ ဖတ်ပါ
>
> GitHub မှာ **ကိုယ့် PR ကို ကိုယ်တိုင် approve လုပ်လို့ မရပါဘူး**။ ဒါကြောင့် ဒီ repo မှာ
> `required_approving_review_count` ကို **0** ထားပါတယ် — တစ်ယောက်တည်း demo လုပ်လို့ရအောင်။
>
> ဒါပေမဲ့ ကျန်တဲ့ gate တွေ အားလုံး ဒီအတိုင်း ရှိနေသေးတယ် —
> CI အောင်မှ merge ရတယ်၊ main ကို တိုက်ရိုက် push မရဘူး (Act 5 punchline မပျက်ပါဘူး)။
>
> **Act 4 မှာ ပြောရမယ့်စကား:** “ဒီ repo မှာ approval 0 ထားတာက ကျွန်တော် တစ်ယောက်တည်း
> ဖြစ်လို့ပါ။ ခင်ဗျားတို့ team မှာတော့ **1 ထားပါ** — Settings ထဲမှာ ဒီလိုပါပဲ။”
> (ပြီးရင် Settings → Branches page ကို ဖွင့်ပြပါ)
>
> ရှေ့တင်မှာ လုပ်ဖော်ကိုင်ဖက် တစ်ယောက် ရှိရင် — သူ့ကို collaborator ထည့်ပြီး
> live approve ခိုင်းတာက ပိုအားရစရာကောင်းပါတယ်။ အဲဒါဆိုရင် approvals ကို 1 ပြန်ထားပါ:
> ```bash
> gh api -X PUT repos/azpzadev/pr-workflow-demo/branches/main/protection \
>   --input - <<'JSON'
> { "required_status_checks": {"strict": true, "contexts": ["Pest tests", "Pint (code style)"]},
>   "enforce_admins": true,
>   "required_pull_request_reviews": {"required_approving_review_count": 1, "dismiss_stale_reviews": true},
>   "restrictions": null, "allow_force_pushes": false, "allow_deletions": false,
>   "required_conversation_resolution": true }
> JSON
> ```

---

## ACT 1 — လက်ရှိနည်း: တိုက်ရိုက် merge (၃ မိနစ်)

> ပြောစရာ: "ဒါက ခုနေ ကျွန်တော်တို့ လုပ်နေကျပုံပါ။"

```bash
git switch -c feature/coupon
cp demo/01-buggy-DiscountService.php app/Services/DiscountService.php
git add -A && git commit -m "add coupon support"

# ဒီနေရာမှာ — review မရှိ၊ CI မရှိ၊ တန်းထည့်လိုက်တာပဲ
git switch main
git merge feature/coupon
```

```bash
./vendor/bin/pest
```

🔴 **test ၂ ခု ကျ** — `clamps a percentage above 100`, `clamps a negative percentage`

> ပြောစရာ: "ဒါက ခု ကျွန်တော်တို့ကံကောင်းလို့ local မှာ တွေ့တာ။
> တကယ့်လက်တွေ့မှာ ဒါက dev branch ထဲ ရောက်သွားပြီးမှ QA က တွေ့တယ်။
> အဲဒီအချိန်မှာ ကျန်တဲ့ ၅ ယောက်လုံး ဒီ branch ပေါ်မှာ အလုပ်လုပ်နေပြီ။"

```bash
git reset --hard HEAD~1        # ရှင်းလိုက် — PR နည်းနဲ့ ပြန်စမယ်
```

---

## ACT 2 — PR ဖွင့် → CI က ဖမ်း (၄ မိနစ်)

```bash
git switch feature/coupon
git push -u origin feature/coupon
gh pr create --fill --web
```

- PR page မှာ **CI အလိုအလျောက် စပြေးတာ** ပြပါ
- ❌ `Pest tests — Failing` ဖြစ်လာတယ်
- **"Merge pull request" ခလုတ်က မီးခိုးရောင် ဖြစ်နေတာ** ကို လက်ညှိုးထိုးပြပါ

> ပြောစရာ: "ဒီ bug က dev branch ကို **ဘယ်တော့မှ** မရောက်ဘူး။ ဒီမှာတင် ရပ်သွားပြီ။"

---

## ACT 3 — Review: CI မဖမ်းနိုင်တာတွေ (၄ မိနစ်)

PR ရဲ့ comment box မှာ ရိုက်ပါ —

```
@claude ဒီ PR ကို review လုပ်ပေးပါ
```

(သို့) local terminal မှာ —

```bash
claude
> /code-review
```

**Claude တွေ့သင့်တာတွေ** (test တွေက တစ်ခုမှ မဖမ်းနိုင်ဘူး) —

| # | ပြဿနာ | ဘာလို့ အန္တရာယ်ရှိလဲ |
|---|---|---|
| 1 | `'VIP' => 150` | ၁၅၀% လျှော့ → ဈေးနှုန်း **အနုတ်** ဖြစ်သွားမယ် |
| 2 | `COUPONS[$coupon]` က case-sensitive | user က `devtalk` ရိုက်ရင် လျှော့မရဘူး၊ error လည်း မပြဘူး |
| 3 | `?? 0` — coupon မှားရင် တိတ်တိတ်လျစ်လျူရှု | user က လျှော့ရပြီထင်နေမယ်၊ support ticket ဖြစ်မယ် |
| 4 | `$percent + coupon` စုပေါင်းတာ | business rule ဟုတ်လား? — reviewer မေးသင့်တဲ့ မေးခွန်း |

> ပြောစရာ: "ဒါက အဓိကအချက်ပါ။ **CI က မမှန်တာကို ဖမ်းတယ်၊ review က မသင့်တော်တာကို ဖမ်းတယ်။**
> နှစ်ခုလုံး လိုတယ်။"

လူ reviewer တစ်ယောက်ကို `VIP => 150` လိုင်းပေါ်မှာ **inline comment** တစ်ခု ရိုက်ခိုင်းပါ။

---

## ACT 4 — ပြင် → အစိမ်း → Merge (၂ မိနစ်)

```bash
cp demo/02-fixed-DiscountService.php app/Services/DiscountService.php
cat demo/03-coupon-tests.php | tail -n +2 >> tests/Unit/DiscountServiceTest.php
./vendor/bin/pint
./vendor/bin/pest                       # ✅ အားလုံး အစိမ်း

git add -A && git commit -m "fix: clamp stacked discounts, normalise coupon codes"
git push
```

- PR ပေါ်မှာ CI ✅ ပြန်စိမ်းလာတာ ပြပါ
- Reviewer ကို **Approve** နှိပ်ခိုင်းပါ
- **Squash and merge** နှိပ်ပါ

```bash
git switch main && git pull
git log --oneline -3
```

> ပြောစရာ: "ကြည့်ပါ — feature တစ်ခုလုံး **commit တစ်ကြောင်းတည်း**၊ PR နံပါတ်ပါပြီးသား။
> `git log` ဖတ်ရုံနဲ့ ဘာဖြစ်ခဲ့လဲ သိတယ်။"

---

## ACT 5 — Punchline (၂ မိနစ်)

### (က) မှားလို့ မဖြစ်နိုင်တော့ဘူး

```bash
echo "// oops" >> app/Services/DiscountService.php
git commit -am "quick fix"
git push origin main
```

🔴 `remote: error: GH006: Protected branch update failed`

> ပြောစရာ: "ည ၁၁ နာရီ ငိုက်ငိုက်မျဉ်းစင်းနဲ့ 'quick fix' တစ်ခု တိုက်ရိုက်ထည့်ချင်တဲ့အခါ —
> GitHub က ကျွန်တော့်ကို ကယ်လိုက်တာ။"

```bash
git reset --hard origin/main
```

### (ခ) ပြန်ရုပ်တာ click တစ်ချက်

PR page → **Revert** ခလုတ် နှိပ်ပြပါ။ Revert PR အလိုအလျောက် ဖွင့်ပေးတယ်။

> ပြောစရာ: "feature တစ်ခုလုံး ၃ စက္ကန့်နဲ့ ပြန်ထွက်သွားတယ်။
> commit တွေ ရောနေတဲ့ dev branch မှာ ဒါကို လုပ်ကြည့်ပါ — ဘယ်လောက်ကြာမလဲ?"

---

## အရေးပေါ် Plan B

WiFi/CI ပျက်ရင် — အောက်ကနည်းနဲ့ local ပဲ ပြပါ:

```bash
git switch -c feature/coupon
cp demo/01-buggy-DiscountService.php app/Services/DiscountService.php
./vendor/bin/pest        # 🔴 ဒါက CI လုပ်မယ့်အလုပ်ကို local မှာ ပြတာ
cp demo/02-fixed-DiscountService.php app/Services/DiscountService.php
./vendor/bin/pest        # ✅
```

PR screen တွေအတွက် **screenshot တွေ ကြိုရိုက်ထားပါ** — WiFi မကောင်းရင် အသက်ကယ်လိမ့်မယ်။
