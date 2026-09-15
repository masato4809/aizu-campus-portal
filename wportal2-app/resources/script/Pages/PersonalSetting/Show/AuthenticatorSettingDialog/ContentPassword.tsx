import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { useMutationAppPersonalSettingSpPassword } from '@/script/Hooks/App/Mutations/useMutationAppPersonalSettingSPPassword';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { E_COLOR } from '@/script/Enum/EColor';
import { InputField } from '@/script/Component/Form/InputField';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { E_STATUS_CODE } from '@/script/Enum/Server/App/EStatusCode';
import {
  IFormPasswordValidationResult,
  initialResult,
  parseServerValidation,
  validateAll,
} from '@/script/Pages/PersonalSetting/Show/AuthenticatorSettingDialog/ContentPasswordValidation';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

export interface IFormPassword {
  password: string;
}

interface IProps extends IPropsBase {
  handleClose: () => void;
  handleComplete: (url: string) => void;
}
export const ContentPassword: React.FC<IProps> = ({
  handleClose,
  handleComplete,
}) => {
  const { onStart, onFinish } = useProgressContext();
  const [formPassword, setFormPassword] = React.useState<IFormPassword>({
    password: '',
  });
  const [validation, setValidation] =
    React.useState<IFormPasswordValidationResult>(initialResult);
  const [, mutationPassword] = useMutationAppPersonalSettingSpPassword();

  /**
   * パスワードの送信.
   */
  const handleSubmitPassword = () => {
    // バリデーション実行.
    const validation = validateAll(formPassword);
    setValidation(validation);

    // エラーがある場合は処理をしない.
    if (validation.hasError) {
      return;
    }

    onStart();
    mutationPassword({ password: formPassword.password }, []).then(result => {
      switch (result.statusCode) {
        case E_STATUS_CODE.OK:
          handleComplete(result.url);
          break;
        case E_STATUS_CODE.UNPROCESSABLE_ENTITY:
          setValidation(parseServerValidation(result));
          break;
        default:
          break;
      }
      onFinish();
    });
  };

  return (
    <>
      <TypoText bold>Step 1/3</TypoText>
      <TypoText sx={{ marginTop: responsiveSpacing(2) }} noWrap={false}>
        スマートフォンからログインする場合のパスワードを入力してください
      </TypoText>
      <TypoText color={E_COLOR.TEXT_RED}>※英数字8文字以上</TypoText>
      <Box
        sx={{
          mx: '0px',
          my: responsiveSpacing(4),
          display: 'flex',
          alignItems: 'center',
        }}
        component="form"
      >
        <TypoText sx={{ width: responsiveSize(200) }}>password</TypoText>
        <InputField
          type="password"
          inputValue={formPassword.password}
          onChange={password => setFormPassword({ password })}
          errors={validation.data.password?.errors}
          visibleError
        />
      </Box>
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'space-between',
        }}
      >
        <ButtonGeneral label="キャンセル" onClick={handleClose} />
        <ButtonGeneral label="送信する" onClick={handleSubmitPassword} />
      </Box>
    </>
  );
};
