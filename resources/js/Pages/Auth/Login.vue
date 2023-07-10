<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3'
import { mdiAccount, mdiAsterisk } from '@mdi/js'
import LayoutGuest from '@/Layouts/LayoutGuest.vue'
import SectionFullScreen from '@/Components/Partials/SectionFullScreen.vue'
import CardBox from '@/Components/Partials/CardBox.vue'
import FormCheckRadioGroup from '@/Components/Partials/FormCheckRadioGroup.vue'
import FormField from '@/Components/Partials/FormField.vue'
import FormControl from '@/Components/Partials/FormControl.vue'
import BaseDivider from '@/Components/Partials/BaseDivider.vue'
import BaseButton from '@/Components/Partials/BaseButton.vue'
import BaseButtons from '@/Components/Partials/BaseButtons.vue'
import FormValidationErrors from '@/Components/FormValidationErrors.vue'
import NotificationBarInCard from '@/Components/Partials/NotificationBarInCard.vue'
import BaseLevel from '@/Components/Partials/BaseLevel.vue'

const props = defineProps({
  canResetPassword: Boolean,
  status: {
    type: String,
    default: null
  }
})

const form = useForm({
  email: '',
  password: '',
  remember: []
})

const submit = () => {
  form
    .transform(data => ({
      ... data,
      remember: form.remember && form.remember.length ? 'on' : ''
    }))
    .post(route('login'), {
      onFinish: () => form.reset('password'),
    })
}
</script>

<template>
  <LayoutGuest>
    <Head title="Login" />

    <SectionFullScreen
      v-slot="{ cardClass }"
      bg="purplePink"
    >
      <CardBox
        :class="cardClass"
        is-form
        @submit.prevent="submit"
      >
        <FormValidationErrors />

        <NotificationBarInCard
          v-if="status"
          color="info"
        >
          {{ status }}
        </NotificationBarInCard>

        <FormField
          label="Email"
          label-for="email"
          help="Please enter your email"
        >
          <FormControl
            v-model="form.email"
            :icon="mdiAccount"
            id="email"
            autocomplete="email"
            type="email"
            required
          />
        </FormField>

        <FormField
          label="Password"
          label-for="password"
          help="Please enter your password"
        >
          <FormControl
            v-model="form.password"
            :icon="mdiAsterisk"
            type="password"
            id="password"
            autocomplete="current-password"
            required
          />
        </FormField>

        <FormCheckRadioGroup
          v-model="form.remember"
          name="remember"
          :options="{ remember: 'Remember' }"
        />

        <BaseDivider />

        <BaseLevel>
          <BaseButtons>
            <BaseButton
              type="submit"
              color="info"
              label="Login"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            />
            <BaseButton
              v-if="canResetPassword"
              route-name="password.request"
              color="info"
              outline
              label="Remind"
            />
          </BaseButtons>
          <Link
            :href="route('register')"
          >
            Register
          </Link>
        </BaseLevel>
      </CardBox>
    </SectionFullScreen>
  </LayoutGuest>
</template>
