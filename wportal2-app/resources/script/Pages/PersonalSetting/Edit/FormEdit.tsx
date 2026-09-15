import * as React from 'react';
import { Box, FormControl } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { LabelValue } from '@/script/Pages/Common/LabelValue';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { InputField } from '@/script/Component/Form/InputField';
import { IFormEditValidationResult } from '@/script/Pages/PersonalSetting/Edit/FormEditValidation';
import {
  DropFile,
  FormEditImageDrop,
} from '@/script/Pages/PersonalSetting/Edit/FormEditImageDrop';
import { InputFieldMultiline } from '@/script/Component/Form/InputFieldMultiline';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

export interface IFormEdit {
  name: string;
  nickname: string;
  birthDate: string | null;
  selfIntroduction: string;
  slackUserId: string;
  slackUserName: string;
  slackTeamId: string;
  upload?: DropFile & File;
}

interface IProps extends IPropsBase {
  formEdit: IFormEdit;
  validation: IFormEditValidationResult;
  handleUpdate: (newValues: Partial<IFormEdit>) => void;
  handleSubmit: () => void;
}
export const FormEdit: React.FC<IProps> = ({
  sx,
  formEdit,
  validation,
  handleUpdate,
  handleSubmit,
}) => {
  return (
    <FormControl
      sx={{
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'start',
        gap: responsiveSpacing(4),
        ...sx,
      }}
    >
      <LabelValue sx={{ width: '100%' }} label="名前" value={formEdit.name} />
      <FormEditImageDrop
        sx={{ width: '100%' }}
        formEdit={formEdit}
        handleUpdate={handleUpdate}
      />
      <Box
        sx={{
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>表示名</TypoText>
        <InputField
          id="form-personal-setting-nickname"
          inputValue={formEdit.nickname}
          onChange={nickname => handleUpdate({ nickname })}
          visibleError
          errors={validation.data.nickname?.errors}
        />
      </Box>
      {/* 生年月日入力フィールド */}
      <Box
        sx={{
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>生年月日</TypoText>
        <InputField
          id="form-personal-setting-birth-date"
          inputValue={formEdit.birthDate ?? ''}
          onChange={birthDate => handleUpdate({ birthDate })}
          visibleError
          errors={validation.data.birthDate?.errors}
          type="date"
        />
      </Box>
      {/* 自己紹介入力フォーム */}
      <Box
        sx={{
          width: '100%',
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>自己紹介</TypoText>
        <Box
          sx={{
            width: '100%',
          }}
        >
          <InputFieldMultiline
            sx={{
              width: '100%',
              marginTop: responsiveSpacing(2),
            }}
            id="form-personal-setting-self-introduction"
            inputValue={formEdit.selfIntroduction ?? ''}
            onChange={selfIntroduction => handleUpdate({ selfIntroduction })}
            errors={validation.data.selfIntroduction?.errors}
            placeHolder="自己紹介を入力してください"
            rows={8}
            fieldSx={{
              width: '100%',
            }}
            inputProps={{
              sx: {
                width: '100%',
                resize: 'vertical',
              },
            }}
          />
        </Box>
      </Box>
      <Box
        sx={{
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>
          SlackユーザーID
        </TypoText>
        <InputField
          id="form-personal-setting-slack-user-id"
          inputValue={formEdit.slackUserId}
          onChange={slackUserId => handleUpdate({ slackUserId })}
        />
      </Box>
      <Box
        sx={{
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>
          Slackユーザー名
        </TypoText>
        <InputField
          id="form-personal-setting-slack-user-name"
          inputValue={formEdit.slackUserName}
          onChange={slackUserName => handleUpdate({ slackUserName })}
        />
      </Box>
      <Box
        sx={{
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>
          SlackチームID
        </TypoText>
        <InputField
          id="form-personal-setting-slack-team-id"
          inputValue={formEdit.slackTeamId}
          onChange={slackTeamId => handleUpdate({ slackTeamId })}
        />
      </Box>
      <Box
        sx={{
          marginLeft: 'auto',
        }}
      >
        <ButtonGeneral
          sx={{
            minWidth: responsiveSize(200),
            alignItems: 'right',
          }}
          label="更新"
          onClick={handleSubmit}
        />
      </Box>
    </FormControl>
  );
};
