import * as React from 'react';
import { GoogleReCaptchaProvider } from 'react-google-recaptcha-v3';
import { IPropsBase } from '@/script/System/System';
import { CommonIndexProvider } from '@/script/Provider/CommonIndexProvider';
import { Login } from '@/script/Pages/Login/Login';

interface IProps extends IPropsBase {}
export const Index: React.FC<IProps> = ({ ...props }) => {
  // noinspection TypeScriptValidateTypes
  return (
    <CommonIndexProvider {...props}>
      <GoogleReCaptchaProvider
        reCaptchaKey={import.meta.env.VITE_RECAPTCHA_SITE_KEY}
      >
        <Login />
      </GoogleReCaptchaProvider>
    </CommonIndexProvider>
  );
};
export default Index;
